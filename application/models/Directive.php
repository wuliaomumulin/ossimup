<?php

namespace App\Models;

use App\Models\Configsystem;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class Directive extends Model
{
    protected $tableName = 'plugin';
    protected $tablePrefix = '';
    protected $main_path = "/work/etc/server";
    protected $engine_path = "/work/etc/server";
    //数据目录
    static public $directives = array();

    public function getDataList()
    {
        return $this->getData();
        // return $this->get_categories();
    }

    //查
    public function getData()
    {
//        $configsystem = new Configsystem;
//        $data = json_decode($configsystem->serverLink('localhost', 'get_alert_rules'), 255);
//        return $data['data'];
        $sp = new \spHttp();
        $data = $sp->vget("http://localhost:40111/alarm");
        return json_decode($data,1);
    }

    //大规则下新增和编辑
    public function directiveUpd($param)
    {
        $sp = new \spHttp();
        $arr = json_decode($param,1);
        $edit = $arr['edit'];
        $arr['attr']['from'] = strval($arr['attr']['from']);
        $arr['attr']['name'] = strval($arr['attr']['name']);
        $arr['attr']['occurrence'] = strval($arr['attr']['occurrence']);
        $arr['attr']['reliability'] = strval($arr['attr']['reliability']);
        $arr['attr']['protocol'] = strval($arr['attr']['protocol']);
        $arr['attr']['plugin_id'] = strval($arr['attr']['plugin_id']);
        $arr['attr']['plugin_sid'] = strval($arr['attr']['plugin_sid']);
        $arr['attr']['port_from'] = strval($arr['attr']['port_from']);
        $arr['attr']['port_to'] = strval($arr['attr']['port_to']);
        $arr['attr']['to'] = strval($arr['attr']['to']);
        $arr['sid'] = strval($arr['sid']);
        unset($arr['edit']);
        $rs = json_encode($arr,256);
        if(!empty($edit)){
            $data = $sp->vput("http://localhost:40111/alarm",$rs);
        }else{

            $data = $sp->vpost("http://localhost:40111/alarm",$rs);

        }


        return json_decode($data,1);

    }

    //大规则的新增和编辑
    public function directiveAdd($param)
    {
        $sp = new \spHttp();
        $arr = json_decode($param,1);
     //   $arr['attr'] = json_decode(htmlspecialchars_decode($arr['attr'][0]),1);
        $arr['attr']['name'] = strval($arr['attr']['name']);
        $arr['attr']['priority'] = strval($arr['attr']['priority']);
        $arr['kingdom'] = strval($arr['kingdom']);
        $arr['category'] = strval($arr['category']);
        $arr['subcategory'] = strval($arr['subcategory']);
        $arr['sid'] = strval($arr['sid']);
        if(!empty($arr['attr']['id'])){
            $arr['attr']['id'] = strval($arr['attr']['id']);
        }
        $rs = json_encode($arr,256);

        if(!empty($arr['attr']['id'])){

            $data = $sp->vpost("http://localhost:40111/alarm",$rs);
        }else{

        $data = $sp->vput("http://localhost:40111/alarm",$rs);
    }

        return json_decode($data,1);
    }


    //删
    public function directiveDel($param)
    {
        $sp = new \spHttp();
        $data = $sp->vdel("http://localhost:40111/alarm",$param);
        return json_decode($data,1);
    }

    //maxID
    public function getMaxID()
    {
        $sp = new \spHttp();
        $data = $sp->vget("http://localhost:40111/alarm/maxid");
        $res = json_decode($data,1);
        return $res['ID'];
    }

    public function getPluginId()
    {
       $data = $this->field('id')->select();
       return $data;
    }

//    public function getPluginSid($pluginId)
//    {
//        $data = $this->table('plugin_sid')->field('sid')->where(['plugin_id' => $pluginId])->page(0,50)->select();
//        return $data;
//    }

    public function searchPluginSid($params)
    {
        
        if(!empty($params['id']) && !empty($params['plugin_keyWords'])){
            $res = $this->table('plugin_sid')->field('sid')->where(['plugin_id' => $params['id'],'sid' => ['like','%'.$params['plugin_keyWords'].'%']])->page(0,50)->select();
        }elseif (!empty($params['id'])){
        $res = $this->table('plugin_sid')->field('sid')->where(['plugin_id' => $params['id']])->page(0,50)->select();
    }
        return $res;
    }



    public function get_categories($query = '')
    {
        $categories = array();
        $aux_actives = array();

        // 1-. Feed directives (alienvault-*.xml)
        if (file_exists($this->main_path . "/directives.xml")) {

            $f = fopen($this->main_path . "/directives.xml", "r");

            while (!feof($f)) {
                $line = trim(fgets($f));
                if (preg_match("/\<\!ENTITY (.*) SYSTEM '(.*)'>/", $line, $found)) {
                    $aux_actives[$found[1]] = 0;
                }
                if (preg_match("/^\s*\&(.*);/", $line, $found)) {
                    $aux_actives[$found[1]] = 1;
                }
            }
            fclose($f);
        }

        // 2-. Selected engine directives (user.xml)
        if (file_exists($this->engine_path . "/directives.xml")) {
            $f = fopen($this->engine_path . "/directives.xml", "r");
            while (!feof($f)) {
                $line = trim(fgets($f));
                if (preg_match("/\<\!ENTITY (.*) SYSTEM '(.*)'>/", $line, $found)) {
                    $aux_actives[$found[1]] = 0;
                }
                if (preg_match("/^\s*\&(.*);/", $line, $found)) {
                    $aux_actives[$found[1]] = 1;
                }
            }
            fclose($f);
        }

        $categories_xml = $this->get_xml($this->main_path . "/categories.xml");
        if ($categories_xml) {

            foreach ($categories_xml->category as $category) {

                if (!isset($aux_actives[str_replace(".xml", '', (string)$category['xml_file'])])) {
                    continue;
                }


                $file = (file_exists($this->engine_path . "/" . (string)$category['xml_file'])) ? $this->engine_path . "/" . (string)$category['xml_file'] : $this->main_path . "/" . (string)$category['xml_file'];
                // User Contributed must be in engine folder
                if ((string)$category['xml_file'] == "user.xml") {
                    $file = $this->engine_path . "/user.xml";
                }
                if (file_exists($file)) {
                    // $lines = file($file);
                    // foreach ($lines as $line)
                    // {
                    //     if (preg_match("/directive id\=\"(\d+)\"\s+name\=\"([^\"]+)\"\s+priority\=\"(\d+)\"/",$line,$found))
                    //     {
                    //         if ($query != '')
                    //         {
                    //             $pattern = preg_quote($query, "/");
                    //             if (!preg_match("/$pattern/i",$found[2]))
                    //             {
                    //                 continue;
                    //             }
                    //         }
                    //         $directives[$found[1]] = array('name' => $found[2], 'priority' => $found[3]);

                    //     }
                    // }
                    static::parser($file);

                }
            }

        }

        return static::$directives;

    }


    public function get_xml($file, $mode = "SimpleXML")
    {
        if (file_exists($file)) {
            $string = file_get_contents($file);
            // Add '<directives>' root node (patch for simplexml_load_string FALSE returns)
            if (preg_match("/\<directive id/", $string)) {
                $string = preg_replace("/(\<\?xml version[^\>]+\>)/", "\\1\n<directives>", $string) . "</directives>";
                // Empty file with xml header
            } elseif ($string != '' && preg_match("/(\<\?xml version[^\>]+\>)/", $string)
                && !preg_match("/\<\?xml version[^\>]+\>.*\</", $string) && !preg_match("/categories\.xml/", $file)) {
                $string = preg_replace("/(\<\?xml version[^\>]+\>)/", "\\1\n<directives>", $string) . "</directives>";
                // Empty file
            } elseif ($string == '') {
                $string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<directives>\n</directives>";
            }
            // SimpleXML
            if ($mode == "SimpleXML") {
                $xml = simplexml_load_string($string);
                if (!$xml) {
                    return null;
                } else {
                    return $xml;
                }
                // DOMXML
            } else {
                $xml = new DOMDocument();
                if (!$xml->loadXML($string)) {
                    return null;
                } else {
                    return $xml;
                }
            }
        }
        return null;
    }

    /**
     *  解析深层级数据
     * @params file 文件名称
     */
    private static function parser($file)
    {

        if ($xml = simplexml_load_file($file)) {

            $res = json_decode(str_replace('@attributes', 'attributes', json_encode($xml)), TRUE);

            $add = self::formatSingleXml($res, $file);


            if (!\Tools::isEmpty($add)) {
                array_push(static::$directives, $add);
            }

        } else {

            $str = file_get_contents($file);
            $preg = '/<directive.*?>(.*?)<\/directive>/is';
            preg_match_all($preg, $str, $temp);

            if (!empty($temp[0])) {

                $result = [];

                foreach ($temp[0] as $pos => $item) {
                    $xml = simplexml_load_string($item);
                    $res = json_decode(str_replace('@attributes', 'attributes', json_encode($xml)), TRUE);
                    $add = self::formatSingleXml($res, $file, $pos);
                    if (is_array($add)) {
                        array_push(static::$directives, $add);
                    }
                }
            }

        }
    }

    /**
     * 格式化Xml
     * @params $res Array xml数据
     * @params $file String 文件名
     * @params $pos int 位置，从0开始，默认为0
     * 最多支持四层嵌套
     */
    private static function formatSingleXml($res, $file, $pos = 0)
    {
        if (!\Tools::isEmpty($res)) {

            //确定唯一标识
            $unique = [
                'file' => $file,
                'pos' => $pos,
            ];

            if (isset($res['attributes'])) {


                $res['attributes'] = array_merge($res['attributes'], $unique);
                //unset($res['@attributes']);

                //将有可能是数组的都转成数组
                // if(!\Tools::isEmpty($res['rule']['rules'])){
                //     $rule = $res['rule']['rules'];
                //     unset($res['rule']['rules']);
                //     $res['rule']['rules'][] = $rule; 
                // }

                // if(!\Tools::isEmpty($res['rule']['rules'][0]['rule']['rules'])){
                //     $rule = $res['rule']['rules'][0]['rule']['rules'];
                //     unset($res['rule']['rules'][0]['rule']['rules']);
                //     $res['rule']['rules'][0]['rule']['rules'][] = $rule; 
                // }
            }


        }
        return $res;
    }

    public function getKingDoms()
    {
        $data = $this->table('alarm_kingdoms')->select();
        return $data;
    }

    public function getCategories()
    {
        $data = $this->table('alarm_categories')->select();
        return $data;
    }
}

?>