<?php

use App\Models\Event;
use GeoIp2\Database\Reader;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class EventController extends Base
{
    protected $event;
    protected $conf;

    public function init()
    {
        parent::init();
        $this->event = new Event();
        $this->conf = './list_conf/siemEvent.json';
    }

    /*
     * 安全事件列表
     * */
    public function queryListAction()
    {

        //$this->event->getPluginsid();
//        $field = "hex(a.id) id,hex(a.agent_ctx) agent_ctx,a.timestamp,a.tzone,hex(a.sensor_id) sensor_id,a.interface,a.type,a.plugin_id,a.plugin_sid,a.protocol,INET6_NTOA(a.src_ip) src_ip,INET6_NTOA(a.dst_ip) dst_ip,a.src_port,a.dst_port,
//        a.event_condition,a.`value`,a.time_interval,a.absolute,a.priority,a.reliability,a.asset_src,a.asset_dst,a.risk_a as risk,a.risk_c,a.alarm,a.filename,a.username,a.password,a.userdata1,a.userdata2,a.userdata3,a.userdata4,a.userdata5,a.userdata6,a.userdata7,a.userdata8,a.userdata9,a.rulename,
//        a.rep_prio_src,a.rep_prio_dst,a.rep_rel_src,a.rep_rel_dst,a.rep_act_src,a.rep_act_dst,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net,a.refs,a.userdata10,a.userdata11,a.userdata12,a.userdata13,a.userdata14,a.userdata15,
//        c.name as eventname";

//       $field = "hex(a.id) id,hex(a.agent_ctx) agent_ctx,a.timestamp,hex(a.sensor_id) sensor_id,a.interface,a.plugin_id,a.plugin_sid,a.protocol,INET6_NTOA(a.src_ip) src_ip,INET6_NTOA(a.dst_ip) dst_ip,a.src_port,a.dst_port,
        //   a.risk_a as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net";
        $field = "hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,
       a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net";

        // $join = "left join plugin_sid c on a.plugin_id = c.plugin_id and a.plugin_sid = c.sid";
        $where = $this->getParames();
        $order = "a.TIMESTAMP DESC";


        $page_size = input('post.page_size', 50);
        $page = input('post.page', 1);


        $data['total_num'] = $this->event->getCount($where);

//        if (empty($data['total_num'])) {
//            jsonError('无数据');
//        }

        $data['total_page'] = ceil($data['total_num'] / $page_size);
//        if ($page < 1 || $page > $data['total_page']) {
//            jsonError('无效请求');
//        }

        $data_list = $this->event->getDataList($field, $join = '', $where, "", $order, $page, $page_size);

        $GeoLite2 = new GeoLite2();

        foreach ($data_list as $k => &$v) {
            $v['eventname'] = $this->event->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            $event_name_id[] = $v['plugin_id'] . '-' . $v['plugin_sid'];
            //  $data['list'][$k]['hostname'] = $this->event->getHoseName($v['sensor_id']);
            $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
            $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
            $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
            $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
            $v['protocol'] = self::getPro($v['protocol']);
            $v['device_ip'] = $this->event->getDeviceIp($v['device_id']);
        }

        $data['list'] = $data_list;


        //事件查询
        $events = self::getEventSel();
        //协议
        $protocol = self::getProtocol();
        //几区采集器
        $sensor = self::getSensor();

        $json = file_get_contents($this->conf);
        $config = json_decode($json, 256);
        foreach ($config as $key => &$value) {
            if ($value['title'] == '事件名称') {
                $value['valueField'] = $events['ids'];
                $value['textField'] = $events['names'];
            }
            if ($value['title'] == '协议') {
                $value['valueField'] = $protocol['number'];
                $value['textField'] = $protocol['protocol'];
            }
            if ($value['title'] == '设备分类') {
                $value['valueField'] = $sensor['ip'];
                $value['textField'] = $sensor['name'];
            }
        }
        $data['config'] = $config;
        // file_put_contents($this->conf,json_encode($config,1));
        jsonResult($data);
    }

    private function getEventSel()
    {
        return $this->event->getEventSel();
    }

    /**
     * 获取搜索条件
     */
    private function getParames()
    {
        $request = input('post.');
        if (!Tools::isEmpty($request)) {
            return $request;
        }

//        if(!empty($request['begindate']) && !empty($request['enddate'])){
//            $where['begindate'] = $request['begindate'];
//            $where['enddate'] = $request['enddate'];
//
//
//        }
//        unset($request['page'],$request['page_size'],$request['begindate'],$request['enddate']);
//        //var_dump($request);die;
//        //默认等于匹配
//        if(!Tools::isEmpty($request)){
//            foreach ($request as $k => $v){
//                $where[$k] = $v;
//            }
//
//        }

//        $where['begindate'] = input('post.begindate','');//时间范围
//        $where['enddate'] = input('post.enddate','');
//
//        $where['eventname'] = input('post.eventname','');//事件名称
//        $where['risk'] = input('post.risk','');//重要程度
//        $where['protocol'] = input('post.protocol','');//通信协议
//        $where['dst_hostname'] = input('post.dst_hostname','');//所属资产
//        $where['src_ip'] = input('post.src_ip','');//源IP
//        $where['dst_ip'] = input('post.dst_ip','');//目标IP
//        $where['src_port'] = input('post.src_port','');//源端口
//        $where['dst_port'] = input('post.dst_port','');//目标端口
//
        //  var_dump($where);die;

    }

    public function getInfoAction()
    {
        $id = $_GET['id'];//?'':'32A811EA9A5E0010F365D162B32CF9AA';
        /*    $plugin_id = $_GET['plugin_id']?'':4004;
            $plugin_sid = $_GET['plugin_sid']?'':3;*/


        //hex(a.id) id,hex(a.ctx) agent_ctx,a.timestamp,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,
        //   a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net


        $field = "hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.tzone,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,
       a.ip_proto as priority,a.ossim_priority as priority,a.ossim_reliability as reliability,a.ossim_asset_src as asset_src,a.ossim_asset_dst as asset_dst,a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net,c.name";
        // $join = "left join plugin_sid c on a.plugin_id = c.plugin_id and a.plugin_sid = c.sid";
        $where = ['a.id' => ["exp", "= unhex('{$id}')"]];
        $group = 'a.id';
        $join = 'left join alienvault_siem.device b on a.device_id = b.id left join alienvault.udp_sensor c on INET6_NTOA(b.device_ip) = c.ip';
        $data = $this->event->getOneStats($field, $join, $where, $group);

        $GeoLite2 = new \GeoLite2();
        $data['eventname'] = $this->event->getEventName(['plugin_id' => $data['plugin_id'], 'sid' => $data['plugin_sid']]);
        $data['device_ip'] = $this->event->getDeviceIp($data['device_id']);
        $data['src_flag'] = strtolower($GeoLite2->getisoCode($data['src_ip']));
        $data['dst_flag'] = strtolower($GeoLite2->getisoCode($data['dst_ip']));
        $data['src_flag_name'] = $GeoLite2->getIpCountryName($data['src_ip']);
        $data['dst_flag_name'] = $GeoLite2->getIpCountryName($data['dst_ip']);
        $data['protocol'] = self::getPro($data['protocol']);

        //查询拓展字段
        $expand = $this->event->getExpand($id);

        $rs['info'] = [];
        $rs['src'] = [];
        $rs['dst'] = [];
        $rs['expand'] = [];
        $rs['data'] = [];
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
        array_push($rs['info'], ['name' => '事件名称', 'value' => $data['eventname']], ['name' => '事件ID', 'value' => $data['id']], ['name' => '插件ID', 'value' => $data['plugin_id']], ['name' => '子插件ID', 'value' => $data['plugin_sid']], ['name' => '协议类型', 'value' => $data['protocol']], ['name' => '时间', 'value' => $data['timestamp']], ['name' => '等级', 'value' => $risk],['name' => '采集IP', 'value' => $data['device_ip']?$data['device_ip']:'未知'],['name' => '所属采集器', 'value' => $data['name']?$data['name']:'未知']);
        array_push($rs['src'], ['name' => '源IP', 'value' => ['ip' => $data['src_ip'], 'flag' => $data['src_flag'], 'flag_name' => $data['src_flag_name']]], ['name' => '源端口', 'value' => $data['src_port']], ['name' => '源设备地址', 'value' => $data['src_host']], ['name' => '源MAC地址', 'value' => $data['src_mac']], ['name' => '源网络地址', 'value' => $data['src_net']],['name' => '源资产名称', 'value' => $data['src_hostname']]);
        array_push($rs['dst'], ['name' => '目标IP', 'value' => ['ip' => $data['src_ip'], 'flag' => $data['src_flag'], 'flag_name' => $data['src_flag_name']]], ['name' => '目标端口', 'value' => $data['src_port']], ['name' => '目标设备地址', 'value' => $data['dst_host']], ['name' => '目标MAC地址', 'value' => $data['dst_mac']], ['name' => '目标网络地址', 'value' => $data['dst_net']],['name' => '目标资产名称', 'value' => $data['dst_hostname']]);
        array_push($rs['expand'],['name' => '拓展字段1', 'value' => $expand['userdata1']],['name' => '拓展字段2', 'value' => $expand['userdata2']],['name' => '拓展字段3', 'value' => $expand['userdata3']],['name' => '拓展字段4', 'value' => $expand['userdata4']],['name' => '拓展字段5', 'value' => $expand['userdata5']],['name' => '拓展字段6', 'value' => $expand['userdata6']],['name' => '拓展字段7', 'value' => $expand['userdata7']],['name' => '拓展字段8', 'value' => $expand['userdata8']],['name' => '拓展字段9', 'value' => $expand['userdata9']]);
        array_push($rs['data'],['name' => '数据值','value' => $expand['data_payload']]);
        jsonResult($rs);
    }

    /*
    * 获取列表显示配置
    * */
    public function getListConfigAction()
    {
        $data = $this->event->getListConfig();
        jsonResult($data);
    }


    //获取协议
    public function getProtocolAction()
    {
        $data = $this->alarm->getProtocol();
        jsonResult($data);
    }

    //获取单个协议
    public function getPro($number)
    {
        $data = $this->event->getPro($number);
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

    private function getProtocol()
    {
        return $this->event->pro();
    }

    private function getSensor()
    {
        return $this->event->sensor();
    }
}

?>