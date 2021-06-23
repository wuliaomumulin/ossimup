<?php

use App\Models\Event;
use GeoIp2\Database\Reader;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class EventController extends Base
{
    protected $event;
    protected $conf;
    protected $num = 12;

    public function init()
    {
        parent::init();
        $this->event = new Event();
        $this->conf = './list_conf/siemEvent.json';
        $this->checkAuth($this->num);
    }

    /*
     * 安全事件列表
     * */
    public function queryListOldAction()
    {

        $field = "hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,
       a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net";

        $where = $this->getParames();
        $order = "a.TIMESTAMP DESC";

        $page_size = input('post.page_size', 50);
        $page = input('post.page', 1);


        $data['total_num'] = $this->event->getCount($where);

        $data['total_page'] = ceil($data['total_num'] / $page_size);

        if (count($where) <= 2) {
            $redis = new \phpredis();
            if ($redis->get('event-page-' . $page)) {
                $data_list = json_decode($redis->get('event-page-' . $page), 1);
            } else {
                $data_list = $this->event->getDataList($field, $join = '', $where, "", $order, $page, $page_size);
                $redis->set('event-page-' . $page, json_encode($data_list, 256), 0, 0, 30);
            }
        } else {
            $data_list = $this->event->getDataList($field, $join = '', $where, "", $order, $page, $page_size);
        }


        $GeoLite2 = new GeoLite2();

        foreach ($data_list as $k => &$v) {
            if (!empty($this->event->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]))) {
                $v['eventname'] = $this->event->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            } else {
                unset($data_list[$k]);
            }

            $event_name_id[] = $v['plugin_id'] . '-' . $v['plugin_sid'];

            if (filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
                $v['src_flag'] = '';
                $v['src_flag_name'] = '';
            } else {
                $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
                $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
            }

            if (filter_var($v['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
                $v['dst_flag'] = '';
                $v['dst_flag_name'] = '';
            } else {
                $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
                $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
            }

            if (!empty($v['src_mac'])) {
                $v['src_mac'] = substr($v['src_mac'], 0, 2) . '-' . substr($v['src_mac'], 2, 2) . '-' . substr($v['src_mac'], 4, 2) . '-' . substr($v['src_mac'], 6, 2) . '-' . substr($v['src_mac'], 8, 2) . '-' . substr($v['src_mac'], 10, 2);
            }

            if (!empty($v['dst_mac'])) {
                $v['dst_mac'] = substr($v['dst_mac'], 0, 2) . '-' . substr($v['dst_mac'], 2, 2) . '-' . substr($v['dst_mac'], 4, 2) . '-' . substr($v['dst_mac'], 6, 2) . '-' . substr($v['dst_mac'], 8, 2) . '-' . substr($v['dst_mac'], 10, 2);
            }

            $v['protocol'] = self::getPro($v['protocol']);
            $device_ip_and_hostname = $this->event->getDeviceIp($v['device_id']);
            $v['device_ip'] = $device_ip_and_hostname['device_ip'];
            $v['device_hostname'] = $device_ip_and_hostname['hostname'] ? $device_ip_and_hostname['hostname'] : '未设置';

            $v['src_hostname'] = $this->event->getHostName($v['src_ip']) ? $this->event->getHostName($v['src_ip']) : '未设置';
            $v['dst_hostname'] = $this->event->getHostName($v['dst_ip']) ? $this->event->getHostName($v['dst_ip']) : '未设置';
            $v['agent_ctx'] = $v['agent_ctx'] ? $v['agent_ctx'] : '未设置';
            $v['sensor_id'] = $v['sensor_id'] ? $v['sensor_id'] : '未设置';
            $v['src_host'] = $v['src_host'] ? $v['src_host'] : '未设置';
            $v['src_mac'] = $v['src_mac'] ? $v['src_mac'] : '未设置';
            $v['src_net'] = $v['src_net'] ? $v['src_net'] : '未设置';
            $v['dst_host'] = $v['dst_host'] ? $v['dst_host'] : '未设置';
            $v['dst_mac'] = $v['dst_mac'] ? $v['dst_mac'] : '未设置';
            $v['dst_net'] = $v['dst_net'] ? $v['dst_net'] : '未设置';

            $v['sensor_id'] = $this->event->getEventSensorId($v['device_id']);
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

        jsonResult($data);
    }

    public function queryListAction()
    {
        $redis = new \phpredis();
        $where = $this->getParames();
        //普通车间
        if ($_SESSION['user_device_power'] != 'all' && $_SESSION['user_monitor_power'] != 'all') {
            $user_device_power = array_shift(json_decode($_SESSION['user_device_power'], 1));
            $device_ip = $user_device_power['device_ip'];
            if (count($where) <= 2) {
                if ($redis->get('sensor-ip-'.$device_ip)) {
                    $res = json_decode($redis->get('sensor-ip-'.$device_ip), 1);
                    $database = $this->split($res, 50);
                    $page = input('post.page', 1);
                    $data['list'] = $database[$page - 1];

                    $data['total_num'] = 1000;
                    $data['total_page'] = ceil($data['total_num'] / 50);
                }else{
                    $data['list'] = [];
                    $data['total_num'] = 0;
                    $data['total_page'] = 0;
                }
            } else {
                $data = $this->eventSearch($where);
            }

            //超级用户
        } else {

            if (count($where) <= 2) {
                if ($redis->get('sensor-ip-1')) {
                    $res = json_decode($redis->get('sensor-ip-1'), 1);
                    $database = $this->split($res, 50);
                    $page = input('post.page', 1);
                    $data['list'] = $database[$page - 1];

                    $data['total_num'] = 1000;
                    $data['total_page'] = ceil($data['total_num'] / 50);
                }else{
                    $data['list'] = [];
                    $data['total_num'] = 0;
                    $data['total_page'] = 0;
                }
            } else {
                $data = $this->eventSearch($where);
            }

        }


        //事件查询
        //  $events = self::getEventSel();   加缓存
        if ($redis->get('eventName')) {
            $events = json_decode($redis->get('eventName'), 1);
        } else {
            $events = self::getEventSel();
            $redis->set('eventName', json_encode($events, 256), 0, 0, 600);
        }

        //协议
        //  $protocol = self::getProtocol();  加缓存
        if ($redis->get('protocol')) {
            $protocol = json_decode($redis->get('protocol'), 1);
        } else {
            $protocol = self::getProtocol();
            $redis->set('protocol', json_encode($protocol, 256), 0, 0, 600);
        }

        //几区采集器
        // $sensor = self::getSensor();    加缓存
        if ($redis->get('sensor')) {
            $sensor = json_decode($redis->get('sensor'), 1);
        } else {
            $sensor = self::getSensor();
            $redis->set('sensor', json_encode($sensor, 256), 0, 0, 600);
        }

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

        jsonResult($data);

    }

    public function eventSearch($where)
    {
        $field = "hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,
       a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net";

        $order = "a.TIMESTAMP DESC";

        $page_size = input('post.page_size', 50);
        $page = input('post.page', 1);

        $data['total_num'] = $this->event->getCount($where);
        $data['total_page'] = ceil($data['total_num'] / $page_size);

        $data_list = $this->event->getDataList($field, $join = '', $where, "", $order, $page, $page_size);

        $GeoLite2 = new GeoLite2();

        foreach ($data_list as $k => &$v) {
            if (!empty($this->event->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]))) {
                $v['eventname'] = $this->event->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            } else {
                unset($data_list[$k]);
            }

            $event_name_id[] = $v['plugin_id'] . '-' . $v['plugin_sid'];

            if (filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
                $v['src_flag'] = '';
                $v['src_flag_name'] = '';
            } else {
                $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
                $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
            }

            if (filter_var($v['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
                $v['dst_flag'] = '';
                $v['dst_flag_name'] = '';
            } else {
                $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
                $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
            }

            if (!empty($v['src_mac'])) {
                $v['src_mac'] = substr($v['src_mac'], 0, 2) . '-' . substr($v['src_mac'], 2, 2) . '-' . substr($v['src_mac'], 4, 2) . '-' . substr($v['src_mac'], 6, 2) . '-' . substr($v['src_mac'], 8, 2) . '-' . substr($v['src_mac'], 10, 2);
            }

            if (!empty($v['dst_mac'])) {
                $v['dst_mac'] = substr($v['dst_mac'], 0, 2) . '-' . substr($v['dst_mac'], 2, 2) . '-' . substr($v['dst_mac'], 4, 2) . '-' . substr($v['dst_mac'], 6, 2) . '-' . substr($v['dst_mac'], 8, 2) . '-' . substr($v['dst_mac'], 10, 2);
            }

            $v['protocol'] = self::getPro($v['protocol']);
            $device_ip_and_hostname = $this->event->getDeviceIp($v['device_id']);
            $v['device_ip'] = $device_ip_and_hostname['device_ip'];
            $v['device_hostname'] = $device_ip_and_hostname['hostname'] ? $device_ip_and_hostname['hostname'] : '未设置';

            $v['src_hostname'] = $this->event->getHostName($v['src_ip']) ? $this->event->getHostName($v['src_ip']) : '未设置';
            $v['dst_hostname'] = $this->event->getHostName($v['dst_ip']) ? $this->event->getHostName($v['dst_ip']) : '未设置';
            $v['agent_ctx'] = $v['agent_ctx'] ? $v['agent_ctx'] : '未设置';
            $v['sensor_id'] = $v['sensor_id'] ? $v['sensor_id'] : '未设置';
            $v['src_host'] = $v['src_host'] ? $v['src_host'] : '未设置';
            $v['src_mac'] = $v['src_mac'] ? $v['src_mac'] : '未设置';
            $v['src_net'] = $v['src_net'] ? $v['src_net'] : '未设置';
            $v['dst_host'] = $v['dst_host'] ? $v['dst_host'] : '未设置';
            $v['dst_mac'] = $v['dst_mac'] ? $v['dst_mac'] : '未设置';
            $v['dst_net'] = $v['dst_net'] ? $v['dst_net'] : '未设置';

            $v['sensor_id'] = $this->event->getEventSensorId($v['device_id']);
        }

        $data['list'] = $data_list;
        return $data;
    }

    //数组按指定10个为一组的分割数组
    public function split($data, $num = 5)
    {

        $arrRet = array();
        if (!isset($data) || empty($data)) {
            return $arrRet;
        }

        $iCount = count($data) / $num;
        if (!is_int($iCount)) {
            $iCount = ceil($iCount);
        } else {
            $iCount += 1;
        }
        for ($i = 0; $i < $iCount; ++$i) {
            $arrInfos = array_slice($data, $i * $num, $num);
            if (empty($arrInfos)) {
                continue;
            }
            $arrRet[] = $arrInfos;
            unset($arrInfos);
        }

        return $arrRet;

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
//        $id = $_GET['id'];//?'':'32A811EA9A5E0010F365D162B32CF9AA';
//
//        $field = "hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.tzone,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,
//       a.ip_proto as priority,a.ossim_priority as priority,a.ossim_reliability as reliability,a.ossim_asset_src as asset_src,a.ossim_asset_dst as asset_dst,a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net,c.name";
//
//        $where = ['a.id' => ["exp", "= unhex('{$id}')"]];
//        $group = 'a.id';
//        $join = 'left join alienvault_siem.device b on a.device_id = b.id left join alienvault.udp_sensor c on INET6_NTOA(b.device_ip) = c.ip';
//        $data = $this->event->getOneStats($field, $join, $where, $group);
//
//        $GeoLite2 = new \GeoLite2();
//        $data['eventname'] = $this->event->getEventName(['plugin_id' => $data['plugin_id'], 'sid' => $data['plugin_sid']]);
//        $device_ip_and_hostname = $this->event->getDeviceIp($data['device_id']);
//        $data['device_ip'] = $device_ip_and_hostname['device_ip'];
//        $data['device_hostname'] = $device_ip_and_hostname['hostname'] ? $device_ip_and_hostname['hostname'] : '未设置';
//
//        if (filter_var($data['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
//            $data['src_flag'] = '';
//        } else {
//            $data['src_flag'] = strtolower($GeoLite2->getisoCode($data['src_ip']));
//        }
//
//        if (filter_var($data['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
//            $data['dst_flag'] = '';
//        } else {
//            $data['dst_flag'] = strtolower($GeoLite2->getisoCode($data['dst_ip']));
//        }
//
//        if (filter_var($data['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
//            $data['src_flag_name'] = '';
//        } else {
//            $data['src_flag_name'] = $GeoLite2->getIpCountryName($data['src_ip']);
//        }
//
//        if (filter_var($data['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
//            $data['dst_flag_name'] = '';
//        } else {
//            $data['dst_flag_name'] = $GeoLite2->getIpCountryName($data['dst_ip']);
//        }
//
//        if (!empty($data['src_mac'])) {
//            $data['src_mac'] = substr($data['src_mac'], 0, 2) . '-' . substr($data['src_mac'], 2, 2) . '-' . substr($data['src_mac'], 4, 2) . '-' . substr($data['src_mac'], 6, 2) . '-' . substr($data['src_mac'], 8, 2) . '-' . substr($data['src_mac'], 10, 2);
//        }
//
//        if (!empty($data['dst_mac'])) {
//            $data['dst_mac'] = substr($data['dst_mac'], 0, 2) . '-' . substr($data['dst_mac'], 2, 2) . '-' . substr($data['dst_mac'], 4, 2) . '-' . substr($data['dst_mac'], 6, 2) . '-' . substr($data['dst_mac'], 8, 2) . '-' . substr($data['dst_mac'], 10, 2);
//        }
//
//        $data['protocol'] = self::getPro($data['protocol']);
//        $data['src_hostname'] = $this->event->getHostName($data['src_ip']) ? $this->event->getHostName($data['src_ip']) : '未设置';
//        $data['dst_hostname'] = $this->event->getHostName($data['dst_ip']) ? $this->event->getHostName($data['dst_ip']) : '未设置';
        $data = input('get.');
        //查询拓展字段
        $expand = $this->event->getExpand($data['id']);

        $rs['info'] = [];
        $rs['src'] = [];
        $rs['dst'] = [];
        $rs['expand'] = [];
        $rs['data'] = [];
        if ($data['risk'] == 0) {
            $risk = '普通';
        } elseif ($data['risk'] == 1) {
            $risk = '一般';
        } elseif ($data['risk'] == 2) {
            $risk = '严重';
        } elseif ($data['risk'] == 3) {
            $risk = '紧急';
        } elseif ($data['risk'] == 4) {
            $risk = '特急';
        }
        array_push($rs['info'], ['name' => '事件名称', 'value' => $data['eventname']?$data['eventname']:'未知'], ['name' => '事件ID', 'value' => $data['id']], ['name' => '插件ID', 'value' => $data['plugin_id']], ['name' => '子插件ID', 'value' => $data['plugin_sid']], ['name' => '协议类型', 'value' => $data['protocol']?$data['protocol']:'未知'], ['name' => '时间', 'value' => $data['timestamp']], ['name' => '等级', 'value' => $risk], ['name' => '采集IP', 'value' => $data['device_ip'] ? $data['device_ip'] : '未设置'], ['name' => '采集设备', 'value' => $data['device_hostname'] ? $data['device_hostname'] : '未设置'], ['name' => '所属采集器', 'value' => $data['name'] ? $data['name'] : '未设置']);
        array_push($rs['src'], ['name' => '源IP', 'value' => ['ip' => $data['src_ip'], 'flag' => $data['src_flag'], 'flag_name' => $data['src_flag_name']]], ['name' => '源端口', 'value' => $data['src_port']], ['name' => '源MAC地址', 'value' => $data['src_mac'] ? $data['src_mac'] : '未设置'], ['name' => '源资产名称', 'value' => $data['src_hostname'] ? $data['src_hostname'] : '未设置']);
        array_push($rs['dst'], ['name' => '目标IP', 'value' => ['ip' => $data['dst_ip'], 'flag' => $data['dst_flag'], 'flag_name' => $data['dst_flag_name']]], ['name' => '目标端口', 'value' => $data['dst_port']], ['name' => '目标MAC地址', 'value' => $data['dst_mac'] ? $data['dst_mac'] : '未设置'], ['name' => '目标资产名称', 'value' => $data['dst_hostname'] ? $data['dst_hostname'] : '未设置']);
        array_push($rs['expand'], ['name' => '拓展字段1', 'value' => $expand['userdata1']], ['name' => '拓展字段2', 'value' => $expand['userdata2']], ['name' => '拓展字段3', 'value' => $expand['userdata3']], ['name' => '拓展字段4', 'value' => $expand['userdata4']], ['name' => '拓展字段5', 'value' => $expand['userdata5']], ['name' => '拓展字段6', 'value' => $expand['userdata6']], ['name' => '拓展字段7', 'value' => $expand['userdata7']], ['name' => '拓展字段8', 'value' => $expand['userdata8']], ['name' => '拓展字段9', 'value' => $expand['userdata9']]);
        array_push($rs['data'], ['name' => '数据值', 'value' => $expand['data_payload']]);
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