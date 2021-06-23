<?php

use App\Models\Alarm;
use GeoIp2\Database\Reader;
use App\Models\Event;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class AlarmController extends Base
{
    protected $alarm;
    protected $location;
    protected $conf;

    /**
     * 获取告警列表
     */
    public function init()
    {
        parent::init();
        $this->alarm = new Alarm();
        $this->location = new Reader(dirname(dirname(__FILE__)) . '/library/GeoLite2-City.mmdb');
        $this->conf = './list_conf/alarmEvents.json';
    }

    public function queryListAction()
    {

        $field = "hex(a.backlog_id) backlog_id,hex(a.event_id) event_id,hex(a.corr_engine_ctx) corr_engine_ctx,a.timestamp,
                a.status,a.protocol,INET6_NTOA(a.src_ip) src_ip,INET6_NTOA(a.dst_ip) dst_ip,a.src_port,a.dst_port,a.risk,
                a.plugin_id,a.plugin_sid,
	            ki.NAME AS kingdom,
	            ca.NAME AS category,
	            ta.subcategory ";

        $join = "LEFT JOIN (alarm_taxonomy ta
	             LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id
	            LEFT JOIN alarm_categories ca ON ta.category = ca.id 
	            ) ON a.plugin_sid = ta.sid 
	            AND a.corr_engine_ctx = ta.engine_id,
	            backlog b ";

        $where = $this->getParames();

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
        $data_list = $this->alarm->getDataList($field, $join, $where, $group, $order, $page, $page_size);

        $GeoLite2 = new GeoLite2();
        foreach ($data_list as $k => &$v) {
            $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
            $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
            $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
            $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
            $v['protocol'] = self::getPro($v['protocol']);
        }
        $data['list'] = $data_list;

        //事件查询
        $events = self::getCategory();
        $protocol = self::getProtocol();

        $json = file_get_contents($this->conf);
        $config = json_decode($json, 256);
        foreach ($config as $key => &$value) {
            if ($value['title'] == '产生类型') {
                $value['valueField'] = $events['ids'];
                $value['textField'] = $events['names'];
            }
            if($value['title'] == '协议'){
                $value['valueField'] = $protocol['number'];
                $value['textField'] = $protocol['protocol'];
            }
        }

        $data['config'] = $config;
        jsonResult($data);

    }

    /*
     * 获取列表显示配置
     * */
    public function getListConfigAction()
    {
        $data = $this->alarm->getListConfig();
        jsonResult($data);
    }


    /**
     * 获取搜索条件
     */
    private function getParames()
    {
        $request = input('post.');
        if (!Tools::isEmpty($request)) {
            $request['TIMESTAMP'] = '1970-01-01 00:00:00';//默认
            return $request;
        }

//        if(!empty($request['begindate']) && !empty($request['enddate'])){
//            $where['begindate'] = $request['begindate'];
//            $where['enddate'] = $request['enddate'];
//
//            unset($request['page'],$request['page_size'],$request['begindate'],$request['enddate']);
//        }
//        //默认等于匹配
//        if(!Tools::isEmpty($request)){
//            $where[key($request)] = current($request);
//        }
//        $where['begindate'] = input('post.begindate', '');//时间范围
//        $where['enddate'] = input('post.enddate', '');
//        $where['TIMESTAMP'] = '1970-01-01 00:00:00';//默认
//        $where['risk'] = input('post.risk', '');//重要程度
//        $where['alarm_type'] = input('post.alarm_type', '');//类型
//        $where['src_ip'] = input('post.src_ip', '');//源IP
//        $where['dst_ip'] = input('post.dst_ip', '');//目标IP
//        $where['src_port'] = input('post.src_port', '');//源端口
//        $where['dst_port'] = input('post.dst_port', '');//目标端口
//        $where['protocol'] = input('post.protocol', '');//协议

        // return $where;
    }

    /**
     * 获取告警详情
     */
    public function getInfoAction()
    {

        $event_id = input('post.event_id');
        $data = $this->alarm->getOneStats('hex(event_id) as alarm_id,plugin_id,plugin_sid,INET6_NTOA(src_ip) src_ip,INET6_NTOA(dst_ip) dst_ip,src_port,dst_port,protocol,timestamp,status,risk', ['event_id' => ["exp", "= unhex('{$event_id}')"]]);

        $GeoLite2 = new \GeoLite2();
        $data['src_flag'] = strtolower($GeoLite2->getisoCode($data['src_ip']));
        $data['dst_flag'] = strtolower($GeoLite2->getisoCode($data['dst_ip']));
        $data['src_flag_name'] = $GeoLite2->getIpCountryName($data['src_ip']);
        $data['dst_flag_name'] = $GeoLite2->getIpCountryName($data['dst_ip']);
        $data['protocol'] = self::getPro($data['protocol']);

        $rs['info'] = [];
        $rs['src'] = [];
        $rs['dst'] = [];
        if($data['risk'] == 0){
            $risk = '普通';
        }elseif ($data['risk'] == 1){
            $risk = '一般';
        }elseif ($data['risk'] == 2){
            $risk = '严重';
        }elseif ($data['risk'] == 3){
            $risk = '紧急';
        }elseif ($data['risk'] == 4){
            $risk = '特急';
        }
        array_push($rs['info'], ['name' => '事件ID', 'value' => $data['alarm_id']], ['name' => '插件ID', 'value' => $data['plugin_id']], ['name' => '子插件ID', 'value' => $data['plugin_sid']], ['name' => '协议类型', 'value' => $data['protocol']], ['name' => '时间', 'value' => $data['timestamp']], ['name' => '状态', 'value' => $data['status']], ['name' => '等级', 'value' => $risk]);
        array_push($rs['src'], ['name' => '源IP', 'value' => ['ip' => $data['src_ip'], 'flag' => $data['src_flag'], 'flag_name' => $data['src_flag_name']]], ['name' => '源端口', 'value' => $data['src_port']]);
        array_push($rs['dst'], ['name' => '目标IP', 'value' => ['ip' => $data['dst_ip'], 'flag' => $data['dst_flag'], 'flag_name' => $data['dst_flag_name']]], ['name' => '目标端口', 'value' => $data['dst_port']]);

        $rs['event'] = self::relevanceEvent();
        jsonResult($rs);
    }

    /**
     * 获取告警相关的安全事件
     */
    public function relevanceEvent()
    {
        //测试用   实际数据库没有关联上的数据
        $corr_engine_ctx = input('post.corr_engine_ctx');//? '' : '16371233C07A11E8A872493E58020307';
//        $field = "hex(id) id,hex(agent_ctx) agent_ctx,timestamp,tzone,hex(sensor_id) sensor_id,interface,type,plugin_id,plugin_sid,protocol,INET6_NTOA(src_ip) src_ip,INET6_NTOA(dst_ip) dst_ip,src_port,dst_port,
//        event_condition,value,time_interval,absolute,priority,reliability,asset_src,asset_dst,risk_a,risk_c,alarm,filename,username,password,userdata1,userdata2,userdata3,userdata4,userdata5,userdata6,userdata7,userdata8,userdata9,rulename,
//        rep_prio_src,rep_prio_dst,rep_rel_src,rep_rel_dst,rep_act_src,rep_act_dst,src_hostname,dst_hostname,hex(src_mac) src_mac,hex(dst_mac) dst_mac,hex(src_host) src_host,hex(dst_host) dst_host,INET6_NTOA(src_net) src_net,INET6_NTOA(dst_net) dst_net,refs,userdata10,userdata11,userdata12,userdata13,userdata14,userdata15";
        $field = "timestamp,INET6_NTOA(ip_src) src_ip,INET6_NTOA(ip_dst) dst_ip,ossim_risk_c as risk,plugin_id,plugin_sid";

        $where = ['ctx' => ["exp", "= unhex('{$corr_engine_ctx}')"]];
        //  $data['list'] = $this->alarm->getEvent($field, $where);
        //查询关联的安全事件
        $event = new Event();
        $data['list'] = $event->getEvent($field, $where);

        $GeoLite2 = new GeoLite2();
        foreach ($data['list'] as $k => &$v) {
            $v['eventname'] = $this->alarm->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
            $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
            $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
            $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
        }


//        $rs['event'] = [];
//        foreach ($data as $key => $val){
//            array_push($rs['event'],[['name' =>'名称','value' => $v['eventname']],['name' => '风险','value' => $v['risk']],['name' => '时间','value' => $v['timestamp']],['name' => '来源','value' =>['ip'=>$v['src_ip'],'flag' =>$v['src_flag'],'flag_name' => $v['src_flag_name']]],['name' => '目的地','value' =>['ip'=>$v['src_ip'],'flag' =>$v['dst_flag'],'flag_name' => $v['dst_flag_name']]]]);
//        }
//
        $data['config'] = [
            ['key' => "eventname", 'description' => "名称"],
            ['key' => "risk", 'description' => "风险"],
            ['key' => "timestamp", 'description' => "时间"],
            ['key' => "src_ip", 'description' => "来源"],
            ['key' => "dst_ip", 'description' => "目的地"]
        ];

        return $data;
    }


    //获取协议
    public function getProtocolAction()
    {
        if (!\Tools::isEmpty(input('get.name'))) {
            $data = $this->alarm->getProtocol(input('get.name'));
            jsonResult($data);
        }
        $data = $this->alarm->getProtocol();
        jsonResult($data);
    }

    //获取单个协议
    public function getPro($number)
    {
        $data = $this->alarm->getPro($number);
        return $data;
    }

    //修改展示的list
    public function updateListConfigAction()
    {
        $str = input('post.name');
        if (!\Tools::isEmpty($str)) {
            $data = explode(',', $str);
            $config = json_decode(file_get_contents($this->conf), 256);
            foreach ($config as $key => &$val) {
                if (in_array($val['name'], $data) === false) {
                    $val['show'] = false;
                } else {
                    $val['show'] = true;
                }
            }
            if (file_put_contents($this->conf, json_encode($config, 256)) !== false) {
                jsonResult(['status' => true]);
            } else {
                jsonResult(['status' => false]);
            }
        }
    }

//    //触发类型获取
//    public function getKingdomsAction()
//    {
//        if (!\Tools::isEmpty(input('get.name'))) {
//            $data = $this->alarm->getKingdoms(input('get.name'));
//            jsonResult($data);
//        }
//
//        $data = $this->alarm->getKingdoms();
//        jsonResult($data);
//    }

    //产生类型获取
    private function getCategory()
    {
        return $this->alarm->getCategory();
    }

//    //产生子类型获取
//    public function getSubCategoryAction()
//    {
//
//        if (!\Tools::isEmpty(input('get.name'))) {
//            $data = $this->alarm->getSubCategory(input('get.name'));
//            jsonResult($data);
//        }
//
//        $data = $this->alarm->getSubCategory();
//        jsonResult($data);
//    }

    private function getProtocol()
    {
        return $this->alarm->pro();
    }


}

?>

