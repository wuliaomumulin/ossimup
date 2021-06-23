<?php

use App\Models\Event1;
use GeoIp2\Database\Reader;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class Event1Controller extends Base
{
    protected $event;
    protected $conf;
    protected $num = 51;

    public function init()
    {
        parent::init();
        $this->event = new Event1();
        $this->conf = './list_conf/siemEvent1.json';
        $this->checkAuth($this->num);
    }

    /*
     * 安全事件列表
     * */
    public function queryListAction()
    {
        $where = $this->getParames();

        $page = (input('post.page', 1) - 1) * 50;
        $page_size = input('post.page_size', 50);

        //是否含有搜索条件
        if (count($where) <= 2) {
            $redis = new \phpredis();
            if ($redis->get('event-page-' . $page.'-'.$page_size)) {
                $data = json_decode($redis->get('event-page-' . $page.'-'.$page_size), 1);
            } else {

                $data = $this->event->getDataList($where, $page, $page_size);
                $redis->set('event-page-' . $page.'-'.$page_size, json_encode($data, 256), 0, 0, 30);
            }
        } else {
            $data = $this->event->getDataList($where, $page, $page_size);
        }

        $data['total_page'] = ceil($data['total_num'] / $page_size);

        $GeoLite2 = new GeoLite2();

        foreach ($data['list'] as $k => &$v) {
            $eventname = $this->event->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            if (!empty($eventname)) {
                $v['eventname'] = $eventname;

            } else {
                $v['eventname'] = '未知';
            }

            //src_hostname
           // $v['src_hostname'] = $this->event->getDeviceIp($v['src_ip'])?$this->event->getDeviceIp($v['src_ip']):'未设置';
            $v['src_hostname'] = $v['src_ip_desc']?$v['src_ip_desc']:'未设置';
            //dst_hostname
            //$v['dst_hostname'] = $this->event->getDeviceIp($v['dst_ip'])?$this->event->getDeviceIp($v['dst_ip']):'未设置';
            $v['dst_hostname'] = $v['dst_ip_desc']?$v['dst_ip_desc']:'未设置';



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
            } else {
                $v['src_mac'] = '';
            }

            if (!empty($v['dst_mac'])) {
                $v['dst_mac'] = substr($v['dst_mac'], 0, 2) . '-' . substr($v['dst_mac'], 2, 2) . '-' . substr($v['dst_mac'], 4, 2) . '-' . substr($v['dst_mac'], 6, 2) . '-' . substr($v['dst_mac'], 8, 2) . '-' . substr($v['dst_mac'], 10, 2);
            } else {
                $v['dst_mac'] = '';
            }

           // $device_hostname = $this->event->getDeviceIp($v['device']);
           // $v['device_hostname'] = $device_hostname?$device_hostname:'未设置';

            $v['device_hostname'] = $v['device_desc']?$v['device_desc']:'未设置';
            $v['device_ip'] = $v['device'];
            $v['ctx'] = $v['ctx']?$v['agent_ctx']:'未设置';
            $v['sensor_id'] = $v['sensor_id']?$v['sensor_id']:'未设置';
            $v['src_host'] = $v['src_host']?$v['src_host']:'未设置';
            $v['src_mac'] = $v['src_mac']?$v['src_mac']:'未设置';
            $v['src_net'] = $v['src_net']?$v['src_net']:'未设置';
            $v['dst_host'] = $v['dst_host']?$v['dst_host']:'未设置';
            $v['dst_mac'] = $v['dst_mac']?$v['dst_mac']:'未设置';
            $v['dst_net'] = $v['dst_net']?$v['dst_net']:'未设置';
        }

        $data['list'] = array_values($data['list']);
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

    }

    public function getInfoAction()
    {
        $data = input('get.');

        $data['name'] = $data['device_hostname'];
        $data['log'] = htmlspecialchars_decode($data['log']);

        $GeoLite2 = new \GeoLite2();

        $data['device_hostname'] = $data['name'];

        if (filter_var($data['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
            $data['src_flag'] = '';
        } else {
            $data['src_flag'] = strtolower($GeoLite2->getisoCode($data['src_ip']));
        }

        if (filter_var($data['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
            $data['dst_flag'] = '';
        } else {
            $data['dst_flag'] = strtolower($GeoLite2->getisoCode($data['dst_ip']));
        }

        if (filter_var($data['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
            $data['src_flag_name'] = '';
        } else {
            $data['src_flag_name'] = $GeoLite2->getIpCountryName($data['src_ip']);
        }

        if (filter_var($data['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
            $data['dst_flag_name'] = '';
        } else {
            $data['dst_flag_name'] = $GeoLite2->getIpCountryName($data['dst_ip']);
        }

        if (!empty($data['src_mac']) && $data['src_mac'] != '未设置') {
            $data['src_mac'] = substr($data['src_mac'], 0, 2) . '-' . substr($data['src_mac'], 2, 2) . '-' . substr($data['src_mac'], 4, 2) . '-' . substr($data['src_mac'], 6, 2) . '-' . substr($data['src_mac'], 8, 2) . '-' . substr($data['src_mac'], 10, 2);
        }

        if (!empty($data['dst_mac']) && $data['dst_mac'] != '未设置') {
            $data['dst_mac'] = substr($data['dst_mac'], 0, 2) . '-' . substr($data['dst_mac'], 2, 2) . '-' . substr($data['dst_mac'], 4, 2) . '-' . substr($data['dst_mac'], 6, 2) . '-' . substr($data['dst_mac'], 8, 2) . '-' . substr($data['dst_mac'], 10, 2);
        }

        $rs['info'] = [];
        $rs['src'] = [];
        $rs['dst'] = [];
        $rs['expand'] = [];
        $rs['data'] = [];
        if ($data['risk'] == 0 || empty($data['risk'])) {
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
        array_push($rs['info'], ['name' => '事件名称', 'value' => $data['eventname']], ['name' => '事件ID', 'value' => $data['event_id']], ['name' => '插件ID', 'value' => $data['plugin_id']], ['name' => '插件', 'value' => $data['plugin_id_desc']], ['name' => '子插件ID', 'value' => $data['plugin_sid']], ['name' => '子插件', 'value' => $data['plugin_sid_desc']], ['name' => '协议类型', 'value' => $data['protocol']], ['name' => '时间', 'value' => $data['fdate']], ['name' => '等级', 'value' => $risk], ['name' => '采集IP', 'value' => $data['device_ip'] ? $data['device_ip'] : '未设置'], ['name' => '采集设备', 'value' => $data['device_hostname'] ? $data['device_hostname'] : '未设置'], ['name' => '所属采集器', 'value' => $data['device_hostname'] ? $data['device_hostname'] : '未设置'], ['name' => '网口', 'value' => $data['interface'] ? $data['interface'] : '未设置']);
        array_push($rs['src'], ['name' => '源IP', 'value' => ['ip' => $data['src_ip'], 'flag' => $data['src_flag'], 'flag_name' => $data['src_flag_name']]], ['name' => '源端口', 'value' => $data['src_port']],['name' => '源MAC地址', 'value' => $data['src_mac'] ? $data['src_mac'] : '未设置'], ['name' => '源资产名称', 'value' => $data['src_hostname'] ? $data['src_hostname'] : '未设置']);
        array_push($rs['dst'], ['name' => '目标IP', 'value' => ['ip' => $data['dst_ip'], 'flag' => $data['dst_flag'], 'flag_name' => $data['dst_flag_name']]], ['name' => '目标端口', 'value' => $data['dst_port']],  ['name' => '目标MAC地址', 'value' => $data['dst_mac'] ? $data['dst_mac'] : '未设置'],  ['name' => '目标资产名称', 'value' => $data['dst_hostname'] ? $data['dst_hostname'] : '未设置']);
        array_push($rs['expand'], ['name' => '拓展字段1', 'value' => $data['userdata1']], ['name' => '拓展字段2', 'value' => $data['userdata2']], ['name' => '拓展字段3', 'value' => $data['userdata3']], ['name' => '拓展字段4', 'value' => $data['userdata4']], ['name' => '拓展字段5', 'value' => $data['userdata5']], ['name' => '拓展字段6', 'value' => $data['userdata6']], ['name' => '拓展字段7', 'value' => $data['userdata7']], ['name' => '拓展字段8', 'value' => $data['userdata8']], ['name' => '拓展字段9', 'value' => $data['userdata9']]);
        array_push($rs['data'], ['name' => '数据值', 'value' => $data['log']]);
     
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