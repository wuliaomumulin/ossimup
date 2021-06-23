<?php
namespace App\Models;
use App\Models\HostTypes;
/* 
 * 主机卫士
 */
class Zhujws extends \Curl
{
    //protected $host = 'http://192.168.66.155:4000';
    protected $host;
    //伪造请求头
    protected $header;
    //ip组
    protected $ips;
    //是否为主机组
    protected $isHostGroup = false;
    
    protected $conf = './list_conf/Zhujws.json';

    public function __construct(){


        self::getIp();
        //$ip = '192.168.66.155';
        //$this->host = "http://".$ip.":4000";

        $this->header =  array(
            'X-Requested-With: XMLHttpRequest',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
            'Content-Type: application/json; charset=UTF-8',
            'Referer: http://'.$ip.':4000/',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        );
    }

    private function getIp(){
        //默认是一个无效IP
        $ip = "";
        //如果session有值
        if(!empty($_SESSION["user_device_power"])){
            //获取SESSION数据
            $data = json_decode($_SESSION["user_device_power"],1);
            //获取前端传值
            $post = input('device_type/d',"1101,1102,1103");

            //如果传入新的DEVCICE_TYPE(用户在前端做出了选择)
            if(!empty($post)){
                     
                if(array_key_exists($post,  $data)){
                    $ip = $data[$post]["device_ip"];
                }elseif($data == NULL){
                    $ip = (new HostTypes())->getProbeAssetIp($post);

                    if(!is_null($ip)) $ip = array_column($ip,'ip');
                }
            }else{
                 if($data == NULL){
                    $ip = (new HostTypes())->getProbeAssetIp($post);
                }else{
                    //返回第一个元素中的device_ip
                    $ip =  array_shift($data)["device_ip"];
                }
            }
        }

        /*
        * 如果ip个数大于1，就是主机组
        */
        if(stripos($post,',')){
            $this->ips = $ip;
            $this->isHostGroup = true;
        }else{
            //否则就是一个主机
            $this->ips = $ip[0];
            //兼容性代码
            $this->host = "http://".$this->ips.":4000";
        }


        return true;
    }

    /**
    * 测试服务否畅通
    */
    private function circuit(){

        $temp = explode(':',$this->host);
        $temp[1] = ltrim($temp[1],'/');
        if(empty($temp[1])) return false;        
        $msg = \IpLocation::nc_port($temp[1],$temp[2]);
        if($msg == 'no'){

            //如果ips是一个数组，说明需要查询多个设备，所以不需要报错
            if(is_array($this->ips)){
                return false;
            } 
            
            //加入config搜索信息
            $decorator['config'] = json_decode(file_get_contents($this->conf),1);
            jsonError('目标主机不可达',$decorator);
            //jsonError('目标主机不可达',$this->host.'线路不通');

        }

        return true;
    }

    public function login(){
        
        $str = $this->getUserInfo();
        if($str){

            $user = json_decode($str,true);
            if($user['errorCode'] != 0){
                //$config = \Yaf\Registry::get("config");
                $url = $this->host.'/ajax/login';

                $post = '{"username":"andisec","password":"e6e061838856bf47e1de730719fb2609","captcha":"","otp_code":"111111","otp_auth":"0","Access-Token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzcnNTaWQiOjc0MzI2MSwiaWF0IjoxNjAzMjAyMTUxLCJleHAiOjE2MDMyODg1NTF9.H5p6BOlnyHko-Y5It418fVWyc1miavaKbssyMt8y6oI"}';


                $ret = $this->post($url,$post,$this->header);

                $this->setToken($ret);

                return $ret;
            }

        }
        return $str;
    }
    /**
    * 设置token
    */
    private function setToken($ret){
        $arr = json_decode($ret,true);
        if($arr['errorCode'] === 0){

            $redis = new \phpredis();

            $key = __CLASS__.'-'.$_SESSION['uid'];

            return $redis->set($key,$arr['token'],0,0,600);
        }

        return false;
    }
    /**
    * 获取token
    */
    private function getToken(){
        $redis = new \phpredis();
        $key = __CLASS__.'-'.$_SESSION['uid'];
        return $redis->get($key);
    }

    /**
    * 拼接cookies
    */
    private function pushCookies(){
        array_push($this->header,'Cookie: yq-srs-sid=421308; srstoken='.$this->getToken());
    }
    /**
    * @param $ret string 远端返回的结构体字符串
    * @return 替换成平台自己的状态码
    */
    private function replaceStatus($ret){
            $ret = str_replace('errorCode', 'errcode',$ret);
            $ret = str_replace('errorMsg', 'msg', $ret);
            return $ret;
    }

    /**
    * 获取用户信息
    */
    public function getUserInfo(){

        if($this->circuit()){

            $this->pushCookies();

            $url = $this->host.'/ajax/user/'.__FUNCTION__;
            $post = '{"Access-Token":"'.$this->getToken().'"}';


            $ret = $this->post($url,$post,$this->header);

            return $ret;
        }

        return false;
    }

    /**
    * 获取系统信息
    */
    public function getsysinfo(){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/'.__FUNCTION__;
            $post = '{"Access-Token":"'.$this->getToken().'"}';


            $ret = $this->post($url,$post,$this->header);
        }

        return $ret;
    }

    /**
    * 
    */
    public function getGroupList(){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = '{"group":"group","Access-Token":"'.$this->getToken().'"}';


            $ret = $this->post($url,$post,$this->header);
        }

        return $ret;
    }

    /**
    * 主机卫士列表
    */
    public function getHostList($post){

        //如果不是设备组，那么就是单个设备
        if(!$this->isHostGroup) return $this->getOneHostList($post);

        $ret = '';
        $hostlist = array();
        $ips = $this->ips;
        $UdpSensor = new UdpSensor();
        $sensor = array();

        foreach($ips as $ip){
            $this->host = "http://".$ip.":4000";
            $l = self::getOneHostList($post);
            if(!\Tools::isEmpty($l)){
                
                $senso = $UdpSensor->field('host_id,ip,name')->where(['ip'=>$ip])->find();
                $sensor[] = empty($senso) ? [] : $senso;

                $hostlist[] = $l;
            } 
        }
        if(!\Tools::isEmpty($hostlist)){
            $ret = self::getHostListAfter($hostlist,$sensor);
            return is_null($ret) ? false : $ret;
        }

        return false;
        
    }

    /**
    * 主机信息
    */
    public function getHostInfo($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));

            /** Decoraotor */
            $decorator = json_decode($ret,true);
            if(!empty($decorator['hostinfo'])){
                $decorator['hostinfo']['policy_mod_time'] = date('Y-m-d H:i:s',$decorator['hostinfo']['policy_mod_time']);
                $ret = json_encode($decorator); 
            }
        }

        return $ret;
    }

    /**
    * 5.5 主机统计接口
        5.5.1. 获取安全概况
    */
    public function gerAssetStat($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));
            /** Decoraotor */
            $decorator = json_decode($ret,true);
            if(!empty($decorator)){
                $decorator['policy_mod_time'] = date('Y-m-d H:i:s',$decorator['policy_mod_time']);
                $ret = json_encode($decorator); 
            }
        }

        return $ret;
    }
    /**
    * 5.5  批准主机信息
        5.3.4. 批准主机信息
    */
    public function ratifyHostInfo($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));
            /** Decoraotor */
            
        }

        return $ret;
    }
    /**
    * 5.5  修改主机信息
        5.3.5. 修改主机信息
    */
    public function editHostInfo($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));
            /** Decoraotor */
            
        }

        return $ret;
    }
    /**
    * 5.5   删除未批准主机信息
        5.3.6.  删除未批准主机信息
    */
    public function delUnauthHost($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));
            /** Decoraotor */
            
        }

        return $ret;
    }    
    /**
    * 5.5  删除主机信息
        5.3.7. 删除主机信息
    */
    public function delHostInfo($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            //exit($this->post($url,$post,$this->header));
            $ret = self::replaceStatus($this->post($url,$post,$this->header));
            /** Decoraotor */
            
        }

        return $ret;
    }
    /**
    * 
    *   5.5.2. 获取安全事件分类统计
    */
    public function getHostSafetyStatistics($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }
    /**
    * 
    *   5.5.3. 获取安全事件趋势分析
    */
    public function getHostSafetyAnalysis($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }
    /**
    * 
    *   5.5.4. 获取等保评分
    */
    public function getHostScore($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }    

    /**
    * 
    */
    public function getAuditLogList($post){


        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/eventLog/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }
    /**
    * 
    */
    public function getSysAudit($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/syslog/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));
        }
        return $ret;
    }

    /**
    * 系统一键加固
    */
    public function getSystemPolicy($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/policy/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));
        }
        return $ret;
    }
    /**
    * 系统一键加固--开启或关闭
    */
    public function editSystemPolicy($post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/policy/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

           $ret = self::replaceStatus($this->post($url,$post,$this->header));
        }
        return $ret;
    }
    /**
    * 
    */
    public function getSafetyProfile(){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = '{"profile":"profile","Access-Token":"'.$this->getToken().'"}';

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }
    /**
    * 
    */
    public function getSafetyStatistics(){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = '{"getSafetyStatistics":"getSafetyStatistics","Access-Token":"'.$this->getToken().'"}';

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }
    /**
    * 
    */
    public function getSafetyAnalysis(){


        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = '{"getSafetyAnalysis":"getSafetyAnalysis","Access-Token":"'.$this->getToken().'"}';

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }
    /**
    * 
    */
    public function getSafetyTrend(){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = '{"getSafetyTrend":"getSafetyTrend","Access-Token":"'.$this->getToken().'"}';

            $ret = $this->post($url,$post,$this->header);
        }
        return $ret;
    }

    public function getDeviceType(){

        $type =  $_SESSION["user_device_power"];

        $arr = ["1101"=>"区域一","1102"=>"区域二","1103"=>"区域三"];

        if($type != 'all'){

            $data = json_decode($type,1);

            foreach ($arr as $k => $v) {
                if(!array_key_exists($k, $data)){
                       
                    unset($arr[$k]);
                }
            }

        }

        return $arr;
    }
    /**
    * 单个主机在线离线数量
    */
    private function getOneAssetStatus(){

            $post['gid'] = 0;
            $post['tid'] = null;
            $post['status'] = 1;
            $post['search'] = '';
            $post['host_id'] = '';
            $post['page'] = 1;
            $post['limit'] = 10000;
            $post['sort'] = (object)[];

            $ret = $this->login();

            if($ret){

                $url = $this->host.'/ajax/asset/getHostList';
                $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

                $res = $this->post($url,$post,$this->header);

                /** Decoraotor */
                $ret = array_count_values(array_column(json_decode($res,true)['hostlist'],'online_state'));

                return $ret;
                //(new User())->setCache($key, $ret);
            
            }

            //如果用户没有添加任何主机卫士相关采集器，那么默认的资产即为零
            return [0,0];

    }
    /**
    * 设置IP
    */
    public function assetStatus(){

        $redis = new \phpredis();

        $key = $_SESSION['uid'] . '-' . __METHOD__;

        if ($result = $redis->get($key)) {

            return json_decode($result, 1);

        } else {


            $ips = $this->ips;
            $ret = array();

            if(!\Tools::isEmpty($ips)){
                if(is_array($ips)){
                    foreach ($ips as $ip) {
                        $this->host = "http://".$ip.":4000";
                        $ret[] = $this->getOneAssetStatus();
                    }

                    $ret = self::calc_sum($ret);

                }else{
                    $this->host = "http://".$this->ips.":4000";
                    $ret = $this->getOneAssetStatus();
                }

                //设置缓存,5min
                (new User())->setCache($key, $ret,300);
                return $ret;


            }else{
                return false;
            }
        }

    }

    /**
    * 计算组合的数组值，用于设备状态判断
    * $input Array 输入值
    */
    private function calc_sum(Array $input){
        $ret = array(0,0);

        foreach($input as $a){
            $ret[0] += $a[0];
            $ret[1] += $a[1];
        }

        return $ret;
    }
    /**
    * 单台设备主机卫士列表
    */
    private function getOneHostList(Array $post){

        $ret = $this->login();

        if($ret){
            $url = $this->host.'/ajax/asset/getHostList';
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));
            /** Decoraotor */
            $decorator = json_decode($ret,true);

            
          
        /*  if(!empty($decorator['hostlist'])){
                foreach ($decorator['hostlist'] as $k => $a){

                    switch ($a['risk_rank']) {
                        case -1:
                            $decorator['hostlist'][$k]['risk_rank'] = '默认';
                            break;
                        case 0:
                            $decorator['hostlist'][$k]['risk_rank'] = '低';
                            break;
                        case 1:
                            $decorator['hostlist'][$k]['risk_rank'] = '中';
                            break;
                        case 2:
                            $decorator['hostlist'][$k]['risk_rank'] = '高';
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }*/

        }

        //返回格式里面 新增 车间数量
        $decorator["sensor"] = self::getDeviceType();
        //加入config搜索信息
        $decorator['config'] = json_decode(file_get_contents($this->conf),1);
        

        //如果是单主机的资产
        if($this->isHostGroup == false){
            return [
                'config' => $decorator['config'],
                'hostlist' => is_null($decorator['hostlist']) ? [] : $decorator['hostlist'],
                'sensor' => $decorator['sensor'],
                'total' => (string)count($decorator['hostlist']),
            ];
        }

        return $decorator['hostlist'];
    }

    /**
    * 设备主机卫士列表之后的拼装，多个主机列表
    * @params Array $hostlist 主机列表，多维数组
    * @params Array $hostlist 主机列表，多维数组
    * 
    */
    private function getHostListAfter(Array $hostlist,Array $sensor){

        foreach($hostlist as $key => $a){
            foreach ($a as $b) {
               //采集器名称
               $b['sensorname'] = $sensor[$key]['name']; 
               $b['sensorip'] = $sensor[$key]['ip']; 
               $b['sensorid'] = $sensor[$key]['host_id']; 
               
               $ret['hostlist'][] = $b;
            }            
        }
        $ret['total'] = (string)sizeof($ret['hostlist']);

        return $ret;

    }
    /**
    * 安全防护模块
    */
    public function getHostModule(Array $post){
        $ret = $this->login();

        if($ret){


            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));

        }
        return $ret;
    }
    /**
    * 配置安全防护模块
    */
    public function setHostModule(Array $post){        
        $ret = $this->login();
        if($ret){


            $url = $this->host.'/ajax/asset/'.__FUNCTION__;
            $post = json_encode(array_merge($post,['Access-Token'=>$this->getToken()]));

            $ret = self::replaceStatus($this->post($url,$post,$this->header));

        }
        return $ret;
    }
}
