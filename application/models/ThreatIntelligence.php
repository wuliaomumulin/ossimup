<?php

namespace App\Models;

class ThreatIntelligence extends Model{
    
    protected $dbName = 'alienvault_siem';
    protected $tableName = 'intelligence';
    protected $tablePrefix = 'threat_';
    protected $pk = 'id';

    //
    protected $conf = './list_conf/threatIntelligence.json';
    /**
    * 获取列表
    */

    public function getList($where = [],int $page,int $pagesize,$order='a.timestamp desc'): ?array
    {

        /** 加一些测试用例 */
        /*$tables = $this->getTables();
        var_dump($tables);*/

         //配置config
        $json = file_get_contents($this->conf);
        $config = json_decode($json, 256);

        $result['config'] =  $config;
        //field
        $field = 'hex(id) id,severity,family,detail,status,timestamp';
    	
        $result['list'] = $this->comment('查询结果集')->field($field)->alias('a')->where($where)->order($order)->page($page,$pagesize)->select(); 

        $result['list'] = is_null($result['list']) ? [] : $result['list'];

        $result['total_num'] = $this->alias('a')->where($where)->count();
        return $result;
    }

    // 查询成功后的回调方法
    protected function _after_select(&$resultSet, $options) {
        //将数据化解
        if(!\Tools::isEmpty($resultSet)){

            if(\Tools::is_single_array($resultSet)){
               // var_dump($resultSet);
            }else{
                foreach ($resultSet as &$a) {

                   $str = $a['detail'];
                   $str = preg_replace('/[\x00-\x1F\x80-\x9F]/u','',$str);
                   $detail = json_decode($str,true);



                    //exit(json_last_error());


                    if(!empty($detail)){
                        $a['ioc_raw'] = $detail['ioc_raw'];
                        $a['created_at'] = is_numeric($detail['created_at']) ? date('Y-m-d H:i:s',$detail['created_at']) : $detail['created_at'];
                        $a['find_at'] = is_numeric($detail['find_at']) ? date('Y-m-d H:i:s',$detail['find_at']) : $detail['find_at'];
                        $a['update_at'] = is_numeric($detail['update_at']) ? date('Y-m-d H:i:s',$detail['update_at']) : $detail['update_at'];
                        $a['port'] = $detail['port'];
                        $a['related_sample'] = json_encode($detail['related_sample']);
                        $a['related_ip'] = json_encode($detail['related_ip']);
                        $a['related_gangs'] = $detail['related_gangs'];
                        $a['related_gangs_desc'] = $detail['related_gangs_desc'];
                        $a['related_events_and_desc'] = json_encode($detail['related_events_and_desc']);
                        $a['solution'] = $detail['solution'];
                    }
                     unset($a['detail']);
               }
            }

        }
    }

    /**
    * 获取列表,状态为汉字,用于网络安全大屏
    */
    public function screen($where = [],int $page,int $pagesize,$order='a.timestamp desc'): ?array
    {


        //field
        $field = 'hex(id) id,hex(event_id) event_id,case `severity` when "Low" then "低" when "Medium" then "中" when "High" then "高" when "Critical" then "严重" else "未知" end `severity`,family,detail,case `status` when 0 then "未处理" when 1 then "已处理" when 2 then "已忽略" else "未知" end `status`,timestamp';
        
        $result['list'] = $this->comment('查询结果集')->field($field)->alias('a')->where($where)->order($order)->page($page,$pagesize)->select(); 

        $result['list'] = is_null($result['list']) ? [] : $result['list'];
        
        $result['total_num'] = $this->alias('a')->where($where)->count();
        return $result;
    }

    /**
    * 威胁情报迁移到es
    */
   public function querylist()
   {
        $ret = [];
        
         //配置config
        $json = file_get_contents($this->conf);
        $config = json_decode($json, 256);

        $ret['config'] =  $config;

        $Es = new Es();
        $zhongy_Threatintelligence = new \zhongy_Threatintelligence();
        $r = array();
        $post = $zhongy_Threatintelligence->querylist(['abc' => '111']);
        $table = 'threatintelligence';

        $r = $Es->query($table,$post);
        
        if(!\Tools::isEmpty($r['hits']['hits'])){
            foreach ($r['hits']['hits'] as $arr) {
                if(!empty($arr['_source'])){ 
                    $arr['_source']['created_at'] = date('Y-m-d H:i:s',$arr['_source']['created_at']);
                    $arr['_source']['find_at'] = date('Y-m-d H:i:s',$arr['_source']['find_at']);
                    $arr['_source']['update_at'] = date('Y-m-d H:i:s',$arr['_source']['update_at']);
                    $ret['list'][] = $arr['_source'];
                }
            }
        }

        $ret['total_num'] = \Tools::isEmpty($r['hits']['total']['value']) ? 0 : $r['hits']['total']['value'];

        return $ret;
   }
}