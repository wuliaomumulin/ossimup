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
    protected $num = 10;
    //提权列表
    //private 
    /**
     * 获取告警列表
     */
    public function init()
    {
        parent::init();
        $this->alarm = new Alarm();
        $this->location = new Reader(dirname(dirname(__FILE__)) . '/library/GeoLite2-City.mmdb');
        $this->conf = './list_conf/alarmEvents.json';
        $this->checkAuth($this->num);
    }

    public function queryListAction()
    {
        set_time_limit(300);

        $where = $this->getParames();

        $group = "a.similar ";
        $order = "a.TIMESTAMP DESC";

        $page_size = input('post.page_size/d', 50);
        $page = input('post.page/d', 1);

        $data['total_num'] = $this->alarm->getCount($join, $where);

        if (($data['total_num']) > 1000) {
            $data['total_num'] = 1000;
        }

        $data['total_page'] = ceil($data['total_num'] / $page_size);
//        if ($page < 1 || $page > $data['total_page']) {
//            jsonError('无效请求');
//        }



        $data_list = $this->alarm->getDataList($field, $join, $where, $group, $order, $page, $page_size);

        $GeoLite2 = new GeoLite2();
        foreach ($data_list as $k => &$v) {

            if (filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
                $v['src_flag'] = '';
                $v['src_flag_name'] = '';
                $v['dst_flag'] = '';
                $v['dst_flag_name'] = '';
            } else {
                $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
                $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
                $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
                $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
            }
            $v['protocol'] = self::getPro($v['protocol']);

            //查询关联的安全事件
            $name = $this->alarm->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            if (!empty($name)) {
                if (strpos($name, ':') !== false) {
                    $v['alarm_name'] = substr(strrchr($name, ':'), 1);
                } else {
                    $v['alarm_name'] = $name;
                }

            } else {
                $v['alarm_name'] = '未知';
            }

            $event = new Event();
            $v['src_hostname'] = $event->getHostName($v['src_ip']) ? $event->getHostName($v['src_ip']) : '未设置';
            $v['dst_hostname'] = $event->getHostName($v['dst_ip']) ? $event->getHostName($v['dst_ip']) : '未设置';

            $v['kingdom'] = $v['kingdom'] ? $v['kingdom'] : '未知';
            $v['category'] = $v['category'] ? $v['category'] : '未知';
            $v['subcategory'] = $v['subcategory'] ? $v['subcategory'] : '未知';
            $v['corr_engine_ctx'] = $v['corr_engine_ctx'] ? $v['corr_engine_ctx'] : '未知';
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
            if ($value['title'] == '协议') {
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
        $data = input('post.');

        $rs['info'] = [];
        $rs['src'] = [];
        $rs['dst'] = [];

        //获取资产的开放端口和基本信息
        $src_info = $this->alarm->getHostInfo($data['src_ip']);
        $dst_info = $this->alarm->getHostInfo($data['dst_ip']);

        array_push($rs['info'], ['name' => '告警名称', 'value' => $data['alarm_name']], ['name' => '事件ID', 'value' => $data['event_id']], ['name' => '插件ID', 'value' => $data['plugin_id']], ['name' => '子插件ID', 'value' => $data['plugin_sid']], ['name' => '协议类型', 'value' => $data['protocol']], ['name' => '时间', 'value' => $data['timestamp']], ['name' => '状态', 'value' => $data['status']], ['name' => '等级', 'value' => $data['risk']]);
        array_push($rs['src'], ['name' => '源IP', 'value' => ['ip' => $data['src_ip'], 'flag' => $data['src_flag'], 'flag_name' => $data['src_flag_name']]], ['name' => '源资产名称', 'value' => $data['src_hostname']], ['name' => '源端口', 'value' => $data['src_port']],['name' => 'MAC', 'value' => $src_info['mac']],['name' => '资产网口', 'value' => $src_info['interface']],['name' => 'DNS', 'value' => $src_info['fqdns']],['name' => '资产价值', 'value' => $src_info['asset']],['name' => '开放端口', 'value' => $src_info['service']],['name' => '资产种类', 'value' => $src_info['type']],['name' => '所属分区', 'value' => $src_info['sensor']]);
        array_push($rs['dst'], ['name' => '目标IP', 'value' => ['ip' => $data['dst_ip'], 'flag' => $data['dst_flag'], 'flag_name' => $data['dst_flag_name']]], ['name' => '目标资产名称', 'value' => $data['dst_hostname']], ['name' => '目标端口', 'value' => $data['dst_port']],['name' => 'MAC', 'value' => $dst_info['mac']],['name' => '资产网口', 'value' => $dst_info['interface']],['name' => 'DNS', 'value' => $dst_info['fqdns']],['name' => '资产价值', 'value' => $dst_info['asset']],['name' => '开放端口', 'value' => $dst_info['service']],['name' => '资产种类', 'value' => $dst_info['type']],['name' => '所属分区', 'value' => $dst_info['sensor']]);

        $rs['event'] = self::relevanceEvent($data['event_id']);
        $rs['src_ip'] = $data['src_ip'];
        $rs['dst_ip'] = $data['dst_ip'];
        jsonResult($rs);
    }

    /**
     * 获取告警相关的安全事件
     */
    public function relevanceEvent($event_id)
    {
        $redis = new \phpredis();
        if ($redis->get('alarm-event-' . $event_id)) {
            $data = json_decode($redis->get('alarm-event-' . $event_id), 1);
        } else {
            $field = "timestamp,INET6_NTOA(ip_src) src_ip,INET6_NTOA(ip_dst) dst_ip,ossim_risk_c as risk,plugin_id,plugin_sid";
            $where = ['id' => ["exp", "= unhex('{$event_id}')"]];
            $event = new Event();
            $data['list'] = $event->getEvent($field, $where);

            $GeoLite2 = new GeoLite2();
            foreach ($data['list'] as $k => &$v) {
                $v['eventname'] = $this->alarm->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
                $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
                $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
                $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
                $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
                if ($v['risk'] == 0) {
                    $v['risk'] = '普通';
                } elseif ($v['risk'] == 1) {
                    $v['risk'] = '一般';
                } elseif ($v['risk'] == 2) {
                    $v['risk'] = '严重';
                } elseif ($v['risk'] == 3) {
                    $v['risk'] = '紧急';
                } elseif ($v['risk'] == 4) {
                    $v['risk'] = '特急';
                }
            }

            $data['config'] = [
                ['key' => "eventname", 'description' => "名称"],
                ['key' => "risk", 'description' => "风险"],
                ['key' => "timestamp", 'description' => "时间"],
                ['key' => "src_ip", 'description' => "来源"],
                ['key' => "dst_ip", 'description' => "目的地"]
            ];
            $redis->set('alarm-event-' . $event_id, json_encode($data, 256), 0, 0, 300);
        }
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

    /**
     * 确认告警状态
     */

    public function confirmAction()
    {
        $event_id = input('post.event_id');
        if (empty($event_id)) jsonError('缺少参数');
        $where['event_id'] = ['EXP', "= unhex('{$event_id}')"];
        $this->alarm->where($where)->setField('is_read', 1);
        jsonResult();
    }
}

?>

