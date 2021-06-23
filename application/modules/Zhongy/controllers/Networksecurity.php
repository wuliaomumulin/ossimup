<?php
use App\Models\Es;
use App\Models\Alarm;
use App\Models\Event;
use App\Models\ThreatIntelligence;
use App\Models\PluginSid;
use App\Models\UdpSensor;

/* 
 * 网络安全
 */
class NetworksecurityController extends Base{
	protected $model=null;
    protected $es;
    protected $conf;
    protected $num = 23;    //这是为了防止普通用户访问 而故意写了一个超级管理员的权限数字 传入checkAuth（）内，这样肯定不能访问
	public function init(){
        parent::init();

        $this->event = new Event();
        $this->alarm = new Alarm();
        $this->es = new Es();
        $this->checkAuth($this->num);
	}
	/**
	* 实时安全事件
	*/
	public function righttopAction(){

        $zhongy_Networksecurity = new zhongy_Networksecurity();
        $method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Networksecurity->$method();

        $table = 'zn-event';

        $r = $this->es->query($table,$post);
        

        $PluginSid = new PluginSid();

        $ret = [];
        if(!Tools::isEmpty($r['hits']['hits'])){
            foreach ($r['hits']['hits'] as $arr) {
                if(Tools::isEmpty($arr['_source']['plugin_sid_desc'])){
                    $arr['_source']['plugin_sid_desc'] = $PluginSid->getPLuginName($arr['_source']['plugin_id'].'-'.$arr['_source']['plugin_sid']);
                }
                $arr['_source']['src_hostname'] = $this->event->ipToHostname($arr['_source']['src_ip']);  
                $arr['_source']['dst_hostname'] = $this->event->ipToHostname($arr['_source']['dst_ip']);  
                $arr['_source']['device_hostname'] = $this->event->ipToHostname($arr['_source']['device']);
                $arr['_source']['eventname'] = $arr['_source']['plugin_sid_desc'];

                $arr['_source']['fdate'] = date('Y-m-d H:i:s',strtotime($arr['_source']['fdate']));
                $arr['_source']['date'] = date('Y-m-d H:i:s',$arr['_source']['date']);


                $ret[] = $arr['_source'];
            }
        }
        jsonResult($ret);


	}
	/**
	* 实时告警事件
	*/
	public function rightcenterAction(){
        $redis = new \phpredis();

        $key = $_SESSION['uid'] . '-'. __METHOD__;
        if ($result = $redis->get($key)) {

            jsonResult(json_decode($result,true));

        } else {
    		 $field = "hex(a.backlog_id) backlog_id,hex(a.event_id) event_id,hex(a.corr_engine_ctx) corr_engine_ctx,date_format(a.`timestamp`,'%H:%i:%s') `timestamp`,
                    a.status,a.protocol,INET6_NTOA(a.src_ip) src_ip,INET6_NTOA(a.dst_ip) dst_ip,a.src_port,a.dst_port,a.risk,
                    a.plugin_id,a.plugin_sid,
    	            ki.NAME AS kingdom,
    	            ca.NAME AS category,
    	            ta.subcategory ";
            //用户权限
            $user_device_power = json_decode($_SESSION["user_device_power"],1);
            $user_monitor_power = json_decode($_SESSION["user_monitor_power"],1);
            $device_id = [];
           
            foreach ($user_monitor_power as $k => $v) {
                if(!empty($v["device_id"])) $device_id[] = $v["device_id"];
            }

            foreach ($user_device_power as $k => $v) {
               if(!empty($v["device_id"])) $device_id[] = $v["device_id"];
            }

            $join = '';
            if (!empty($device_id)) {
               $join .=" INNER JOIN backlog_event be ON  a.backlog_id = be.backlog_id
        INNER JOIN alienvault_siem.acid_event ae on be.event_id = ae.id AND ae.device_id in (".implode(',', $device_id).") ";
            }


            $join .= "LEFT JOIN (alarm_taxonomy ta
    	             LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id
    	            LEFT JOIN alarm_categories ca ON ta.category = ca.id 
    	            ) ON a.plugin_sid = ta.sid 
    	            AND a.corr_engine_ctx = ta.engine_id,
    	            backlog b ";

            $where = array();

            $group = "a.similar ";
            $order = "a.TIMESTAMP DESC";

            $page_size = input('post.page_size', 50);
            $page = input('post.page', 1);

            $data['total_num'] = $this->alarm->getCount($join, $where);

    //        if (empty($data['total_num'])) {
    //            jsonError('无数据');
    //        }

            $data['total_page'] = ceil($data['total_num'] / $page_size);
    //        if ($page < 1 || $page > $data['total_page']) {
    //            jsonError('无效请求');
    //        }


            // $data['list'] = $this->alarm->getDataList($field, $join, $where, $group, $order, $page, $page_size);

            if(count($where) <= 2){
                $redis = new \phpredis();
                if($redis->get('alarm-page-'.$page)){
                    $data_list = json_decode($redis->get('alarm-page-'.$page),1);
                }else{
                    $data_list = $this->alarm->getDataList($field, $join, $where, $group, $order, $page, $page_size);
                    $redis->set('alarm-page-'.$page,json_encode($data_list,256),0,0,10);
                }
            }else{
                $data_list = $this->alarm->getDataList($field, $join, $where, $group, $order, $page, $page_size);
            }

            $GeoLite2 = new GeoLite2();
            if(!Tools::isEmpty($data_list)){
            	foreach ($data_list as $k => &$v) {
    	            $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
    	            $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
    	            $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
    	            $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
    	            $v['protocol'] = $this->alarm->getPro($v['protocol']);
            	}
            }

            $data['list'] = is_null($data_list) ? array() : $data_list;

            if(!empty($data['list'])){
                $this->alarm->setCache($key,$data,300);
            }

            jsonResult($data);
        }
	}
	/**
	* 实时威胁情报
	*/
	public function rightbottomAction(){
        $page                = input('page',1);
        $pagesize            = input('page_size',50);
        $where = array();
        $ThreatIntelligence = new ThreatIntelligence();
        $datalist = $ThreatIntelligence->screen($where,$page,$pagesize);
        //exit($ThreatIntelligence->_sql());
        jsonResult($datalist);
	}


    /**
    * 前15网络行为访问
    */
    public function lefttopAction(){

        $zhongy_Networksecurity = new zhongy_Networksecurity();
        $method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Networksecurity->$method();


        $table = 'zn-event';

        $r = $this->es->query($table,$post);
        
        $ret = [];
        $keys = ['name','value'];
        $PluginSid = new PluginSid();

        if(!Tools::isEmpty($r['aggregations']['plugin_sid']['buckets'])){
            foreach($r['aggregations']['plugin_sid']['buckets'] as $item) {

                    if(array_key_exists('key',$item)&&sizeof($item)>2) unset($item['key']);

                    $name = $PluginSid->getName(['plugin_id'=>1001,'sid'=>$item['key']]);
                    if(!is_null($name)) $item['key'] = $name;
                    

                    $ret[] = array_combine($keys,$item);
              }


        }

        jsonResult($ret); 
    }

    /**
    * 网络通讯控制事件
    */
    public function leftcenterAction(){

        $zhongy_Networksecurity = new zhongy_Networksecurity();
        $method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Networksecurity->$method();
        //$PluginSid = new PluginSid();

        $table = 'zn-event';
        $UdpSensor = new UdpSensor();

        $r = $this->es->query($table,$post);
        
        $ret = [];
        if(!Tools::isEmpty($r['hits']['hits'])){
            foreach ($r['hits']['hits'] as &$arr) {

                $name =  $UdpSensor->where(['ip' => $arr['_source']['device']])->getField('name');
                $arr['_source']['device'] = is_null($name) ? $arr['_source']['device'] : $name;

                $arr['_source']['name'] = '网络通讯控制（系统事件）';
                $arr['_source']['fdate'] = date('H:i:s',strtotime($arr['_source']['fdate']));

                $ret[] = $arr['_source'];
            }
        }
        jsonResult($ret);
    }

    /**
    * 工控网络安全协议事件 S7、modbus
    */
    public function leftbottomAction(){

        $zhongy_Networksecurity = new zhongy_Networksecurity();
        $method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Networksecurity->$method();

        $table = 'zn-event';

        $r = $this->es->query($table,$post);
        

        $PluginSid = new PluginSid();

        $ret = [];
        if(!Tools::isEmpty($r['hits']['hits'])){
            foreach ($r['hits']['hits'] as $arr) {
				$arr['_source']['fdate'] = date('H:i:s',strtotime($arr['_source']['fdate']));
                if(Tools::isEmpty($arr['_source']['plugin_sid_desc'])){
                    $arr['_source']['plugin_sid_desc'] = $PluginSid->getPLuginName($arr['_source']['plugin_id'].'-'.$arr['_source']['plugin_sid']);
                }
                $arr['_source']['src_hostname'] = $this->event->ipToHostname($arr['_source']['src_ip']);  
                $arr['_source']['dst_hostname'] = $this->event->ipToHostname($arr['_source']['dst_ip']);  
                $arr['_source']['device_hostname'] = $this->event->ipToHostname($arr['_source']['device']);
                $arr['_source']['eventname'] = $arr['_source']['plugin_sid'];

                $arr['_source']['fdate'] = date('H:i:s',strtotime($arr['_source']['fdate']));


                $ret[] = $arr['_source'];
            }
        }
        jsonResult($ret);
    }

    /**
    * 中间地图
    */
    public function centerAction(){


         //print_r(json_decode('{"query":{"bool":{"must":[{"term":{"plugin_id":{"value":1001}}},{"terms":{"plugin_sid":[202800017,202800018,202800019,202800020,202800021,202800022,202800023,202800024,202800025,202800026,202800027,202800028,202800029,202800030,202800031,202800032,200000001,200000002,200000003]}}]}},"size":50,"sort":[{"@timestamp":{"order":"desc"}}]}',true));

    }

}