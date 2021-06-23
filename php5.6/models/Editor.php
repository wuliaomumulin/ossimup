<?php
namespace App\Models;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class Editor extends Model{

    private $AuthorizationCode="";
    private $server="http://localhost:8081";

    const URL = 'etc/agent/plugins/';
    const U = 'etc/agent/';

    public function __construct()
    {
    }
    //获取全部的插件
    public function get_all_plugins($name = ''){
        if(empty($this->AuthorizationCode)){
            if(!$this->getAuthorizationCode()){
                return false;
            }
        }

        $plugins=$this->geturl($this->server."/api/resource/"."etc/agent/plugins");
        $rs = [];

        if (!empty($plugins['items'])) {
            $name = input('get.name');
            foreach ($plugins['items'] as $k => $v) {

                if ((!empty($name) && strpos($v['name'], $name) === false)|| strpos($v['name'], '.cfg') === false)continue;
                $rs[$k]['name'] = $v['name'];
                $rs[$k]['size'] = $v['size'];
                $rs[$k]['last_update'] = $v['modified'];
                $rs[$k]['path'] = '/work'.$v['virtualPath'];
            }
        }
        return array_values($rs);
    }
    //获取全部的启用的插件
    public function get_enabled_plugins($name = '',$type = false){
        if(empty($this->AuthorizationCode)){
            if(!$this->getAuthorizationCode()){
                return false;
            }
        }
        $result=$this->geturl($this->server."/api/resource/"."etc/agent/config.cfg");

        if ($type == false) {
            $result = $this->get_enabled_plugins_list($result,$name);
        }

        return $result;
    }

    public function get_enabled_plugins_list($result,$name = ''){

        $items = explode("\n",$result['content']);

        $begin=0;

        $enabled=array();

        foreach ($items as $item){
            if($item=="[plugins]"){
                $begin=1;continue;
            }
            if($item=="[watchdog]"){
                $begin=0;
            }
            if($begin==1){

                if ((!empty($name) && strpos($item, $name) === false) || empty($item))continue;

                $enabled[]=$item;
            }
        }

        return $enabled;
    }

    //获取单个插件具体的内容
    public function get_plugin_content($url){

        if(empty($this->AuthorizationCode)){
            if(!$this->getAuthorizationCode()){

                return false;
            }
        }
        // echo $ur
        $result=$this->geturl($this->server."/api/resource/".$url);
        //$result=$this->geturl($this->server."/api/resource/"."etc/ossim/agent/plugins/sw.cfg");
        return $result;
    }

    public function save_plugin(){
        if(empty($this->AuthorizationCode)){
            if(!$this->getAuthorizationCode()){
                return false;
            }
        }
    }
    //创建一个文件
    public function createFile($name){
        //明确 后缀
        $File=self::URL . $name;
        //return $File;
        if(empty($this->AuthorizationCode)){
            if(!$this->getAuthorizationCode()){
                return false;
            }
        }
        //$File="etc/ossim/agent/plugins/for_test.cfg";
        $result=$this->posturl($this->server."/api/resource/".$File,"");
        if($result !== false){
            return true;
        }
        return false;
    }
    //写一个文件的内容
    public function save_file_content($name,$content){


        if($name == 'config.cfg'){
            $File=self::U . $name;
        }else{
            $File=self::URL . $name;
        }

        $result=$this->puturl($this->server."/api/resource/".$File,$content);

        if($result !== "Forbidden"){
            return $result;
        }else{
            return "无法连接到控制端";
        }
    }

    //删除一个文件
    public function deleteFile($name){
        if(empty($this->AuthorizationCode)){
            if(!$this->getAuthorizationCode()){
                return false;
            }
        }
        //明确 后缀
        $File=self::URL . $name;
        //$File="etc/ossim/agent/plugins/for_test.cfg";
        $result=$this->delurl($this->server."/api/resource/".$File);
        if($result !== false){
            return true;
        }
        return false;
    }

    public function getAuthorizationCode(){
        $data='{"username":"admin","password":"admin"}';
        $result=$this->auth($this->server."/api/auth/get",$data);
        if($result !== "Forbidden" && strlen($result)>100){
            $this->AuthorizationCode=$result;
            return true;
        }else{
            $this->AuthorizationCode="";
        }
        return false;
    }

    public function auth($url,$data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function geturl($url){
        $authorization = "Authorization: Bearer ".$this->AuthorizationCode;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            $authorization
        ));

        $output = curl_exec($ch);
        curl_close($ch);
        return $this->serverResult($output);
    }

    public function posturl($url,$data){
        $authorization = "Authorization: Bearer ".$this->AuthorizationCode;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            $authorization
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $this->serverResult($output);
    }

    public function puturl($url,$data){
        $authorization = "Authorization: Bearer ".$this->AuthorizationCode;
        $ch = curl_init(); //初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            $authorization
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT"); //设置请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
        $output = curl_exec($ch);

        curl_close($ch);
        return $this->serverResult($output);
    }

    function delurl($url){
        //这个data代表什么
        $authorization = "Authorization: Bearer ".$this->AuthorizationCode;
        $ch = curl_init();
        curl_setopt ($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            $authorization
        ));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $output = curl_exec($ch);
        curl_close($ch);
        return $this->serverResult($output);
    }

    public function serverResult($output){
        if(strpos($output,"Forbidden") !== false){
            return "Forbidden";
        }else if(strpos($output,"Not Found") !== false){
            return "Not Found";
        }else if(strpos($output,"Conflict") !== false){
            return "Conflict";
        }
        return json_decode($output,true);
    }

}
