<?php

namespace App\Models;

use App\Models\Es;

class Event1 extends Model
{
    protected $dbName = 'alienvault_siem';
    protected $tableName = 'acid_event';
    protected $tablePrefix = '';
    protected $pk = 'id';

    public function getCount($where)
    {
        $where = self::getWhere($where);
        if ($_SESSION['user_device_power'] != 'all' && $_SESSION['user_monitor_power'] != 'all' && empty($Adevice)) {

            $str = [];
            if (!empty($_SESSION['user_device_power'])) {
                $user_device_power = array_shift(json_decode($_SESSION['user_device_power'], 1));
                if (!empty($user_device_power['device_id'])) {
                    array_push($str, $user_device_power['device_id']);
                }


            }
            if (!empty($_SESSION['user_monitor_power'])) {
                $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'], 1));
                if (!empty($user_monitor_power['device_id'])) {
                    array_push($str, $user_monitor_power['device_id']);
                }

            }

            $sstr = implode(',', $str);

            $where[] = ['a.device_id' => ["exp", "in ({$sstr})"]];
            $count = $this->alias('a')->where($where)->count('id');

        } else {
            $count = $this->alias('a')->where($where)->count('id');
        }

        return $count;
    }

    private function getWhere($where, $page, $page_size)
    {
        //分权
        if ($_SESSION['user_device_power'] != 'all' && $_SESSION['user_monitor_power'] != 'all') {

            $str = [];
            if (!empty($_SESSION['user_device_power'])) {
                $user_device_power = array_shift(json_decode($_SESSION['user_device_power'], 1));
                if (!empty($user_device_power['device_id'])) {
                    array_push($str, $user_device_power['device_id']);
                }

            }
            if (!empty($_SESSION['user_monitor_power'])) {
                $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'], 1));
                if (!empty($user_monitor_power['device_id'])) {
                    array_push($str, $user_monitor_power['device_id']);
                }
            }

            $params['query']['bool']['must'][]['terms']['device.keyword'] = $str;
        }

        //默认条件
        $params['query']['bool']['must'][]['range']['@timestamp'] = ['lte' => date("Y-m-d", time()) . "T23:59:59.000+0800"];

        if (!empty($where['begindate']) && !empty($where['enddate'])) {

            if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
                $params['query']['bool']['must'] = [];
                $params['query']['bool']['must'][]['range']['@timestamp'] = ['gte' => substr($where['begindate'], 0, 10) . 'T' . substr($where['begindate'], 11) . '.000+0800', 'lte' => substr($where['enddate'], 0, 10) . "T" . substr($where['enddate'], 11) . ".000+0800"];
            }else{
                $params['query']['bool']['must'][1] = [];
                $params['query']['bool']['must'][1]['range']['@timestamp'] = ['gte' => substr($where['begindate'], 0, 10) . 'T' . substr($where['begindate'], 11) . '.000+0800', 'lte' => substr($where['enddate'], 0, 10) . "T" . substr($where['enddate'], 11) . ".000+0800"];
            }

        }

        if (!empty($where['eventname'])) {
            $params['query']['bool']['must'][]['term']['plugin_sid_desc.keyword'] = $where['eventname'];
        }


        if (!empty($where['protocol'])) {
            $params['query']['bool']['must'][]['term']['protocol.keyword'] = $where['protocol'];
        }

        if(!empty($where['src_hostname']) && empty($where['src_ip'])){
            $ip = self::getHostIp($where['src_hostname']);
            $params['query']['bool']['must'][]['term']['src_ip.keyword'] = $ip;
        }

        if(!empty($where['dst_hostname']) && empty($where['dst_ip'])){
            $ip = self::getHostIp($where['dst_hostname']);
            $params['query']['bool']['must'][]['term']['dst_ip.keyword'] = $ip;
        }

        if (!empty($where['src_ip']) && empty($where['src_hostname'])) {
            $params['query']['bool']['must'][]['term']['src_ip.keyword'] = $where['src_ip'];
        }

        if (!empty($where['dst_ip']) && $where['dst_hostname']) {
            $params['query']['bool']['must'][]['term']['dst_ip.keyword'] = $where['dst_ip'];
        }

        if(!empty($where['src_ip']) && !empty($where['src_hostname'])){
            $params['query']['bool']['must'][]['term']['src_ip.keyword'] = $where['src_ip'];
        }

        if(!empty($where['dst_ip'])){
            $params['query']['bool']['must'][]['term']['dst_ip.keyword'] = $where['dst_ip'];
        }

        
        if (!empty($where['src_port'])) {
            $params['query']['bool']['must'][]['term']['src_port.keyword'] = $where['src_port'];
        }

        if (!empty($where['dst_port'])) {
            $params['query']['bool']['must'][]['term']['dst_port.keyword'] = $where['dst_port'];
        }

        if (!empty($where['plugin_id'])) {
            $params['query']['bool']['must'][]['term']['plugin_id.keyword'] = $where['plugin_id'];
        }

        if (!empty($where['plugin_sid'])) {
            $params['query']['bool']['must'][]['term']['plugin_sid.keyword'] = $where['plugin_sid'];
        }

        //几区采集器搜索
        if (!empty($where['device'])) {

            //超级用户 查的所有时 又指定某一个          普通用户查看本区采集器 检测审计下的默认是  然后又指定到某一个
            if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
                $params['query']['bool']['must'][]['term']['device.keyword'] = $where['device'];
            } else {
                $params['query']['bool']['must'][0] = [];
                $params['query']['bool']['must'][0]['term']['device.keyword'] = $where['device'];
            }

        }

        //默认条件
        $params['sort'][]["@timestamp"]["order"] = "desc";
        $params['from'] = $page;
        $params['size'] = $page_size;

        return $params;
    }

    public function getDataList($where, $page, $page_size)
    {
        $params = self::getWhere($where, $page, $page_size);

        $this->es = new Es();

        $data = $this->Format($this->es->query('zn-event-', $params));
        return $data;

    }


    //提取es数据
    private function Format($data)
    {
        $res['list'] = [];
        $res['total_num'] = $data['hits']['total']['value'];
        foreach ($data['hits']['hits'] as $k => $v) {
            $res['list'][] = $v['_source'];
        }
        return $res;
    }

    private function Format1($data)
    {
        $res = [];
        foreach ($data['aggregations']['name']['buckets'] as $k => $v) {
            if (!empty($v['key'])) {
                $res[] = $v['key'];
            }

        }
        return $res;
    }


    public function getOneStats($field, $join = '', $where)
    {
        $data = $this->table('alienvault.host_ip a')->field($field)->join($join)->where($where)->find();
        return $data['hostname'];
    }

    //plugin_sid 获取事件名称
    public function getEventName($param)
    {
        $data = $this->table('alienvault.plugin_sid')->field('name')->where($param)->find();
        //exit($this->_sql());
        return $data['name'];
    }

//    //获取所属资产名
    public function getHoseName($param)
    {
        $data = $this->table('host')->field('host.hostname')->join('host_ip on host_ip.host_id = host.id ')
            ->join('sensor on sensor.ip = host_ip.ip ')->where(['sensor.id' => ["exp", "=unhex('{$param}')"]])->find();
        return $data['hostname'];
    }


    public function getListConfig()
    {
        $where = ['event_list_config'];
        $data = $this->table('config')->where(['conf' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    public function dataDispost($data)
    {
        $i = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
                $res[$v['conf']] = $v['value'];
            } else {
                $res = [$v['conf'] => $v['value']];
            }


        }
        return $res;
    }


    public function getProtocol()
    {
        $data = $this->table('protocol')->field('number,name')->select();
        return $data;
    }

    public function getPro($number)
    {
        $name = $this->table('protocol')->field('name')->where(['number' => $number])->find();
        return $name['name'];
    }

    public function getEvent($field, $where)
    {
        $data = $this->field($field)->where($where)->order('timestamp desc')->limit(5)->select();

        return $data;
    }

    public function getEventSel()
    {
        $this->es = new Es();

        $params['sort'][]['@timestamp']['order'] = 'desc';
        $params['aggs']['name']['terms']['field'] = 'plugin_sid_desc.keyword';
        $params['aggs']['name']['terms']['size'] = 1000;
        $params['_source'] = 'plugin_sid_desc';
        $params['size'] = 0;

        $data = $this->Format1($this->es->query('zn-event-', $params));
        $res = [];
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $res['ids'][] = $v;
                $res['names'][] = $v;
            }
            $res['ids'] = implode(',', $res['ids']);
            $res['names'] = implode(',', $res['names']);
        }


        return $res;
    }

    public function pro()
    {
        $this->es = new Es();

        $params['sort'][]['@timestamp']['order'] = 'desc';
        $params['aggs']['name']['terms']['field'] = 'protocol.keyword';
        $params['aggs']['name']['terms']['size'] = 1000;
        $params['_source'] = 'protocol';
        $params['size'] = 0;

        $data = $this->Format1($this->es->query('zn-event-', $params));
        $res = [];
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $res['number'][] = $v;
                $res['protocol'][] = $v;
            }
            $res['number'] = implode(',', $res['number']);
            $res['protocol'] = implode(',', $res['protocol']);
        }

        return $res;
    }


    public function sensor()
    {
        if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
            $sensor = $this->table('alienvault.udp_sensor')->field('ip,name')->select();
            $data = [];
            foreach ($sensor as $k => $v) {
                $data['ip'][] = $v['ip'];
                $data['name'][] = $v['name'];
            }
            $res['ip'] = implode(',', $data['ip']);
            $res['name'] = implode(',', $data['name']);

        } else {
            $name = [];
            $ip = [];
            if (!empty($_SESSION['user_device_power'])) {
                $user_device_power = array_shift(json_decode($_SESSION['user_device_power'], 1));
                $sensor_name = $this->table('alienvault.udp_sensor')->field('name')->where(['ip' => $user_device_power['device_ip']])->find();
                array_push($name, $sensor_name['name']);
                array_push($ip, $user_device_power['device_ip']);
            }

            if (!empty($_SESSION['user_monitor_power'])) {
                $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'], 1));
                $shenji_name = $this->table('alienvault.udp_sensor')->field('name')->where(['ip' => $user_monitor_power['device_ip']])->find();
                array_push($name, $shenji_name['name']);
                array_push($ip, $user_monitor_power['device_ip']);
            }

            $res['ip'] = implode(',', $ip);
            $res['name'] = implode(',', $name);

        }
        return $res;
    }

    public function getDeviceIp($device)
    {
        $device_ip_hostname = $this->table('alienvault.host_ip a')->field('b.hostname')->join("left join alienvault.host b on a.host_id = b.id")->where(['a.ip' => ["exp", "= INET6_ATON('{$device}')"]])->find();
        return $device_ip_hostname['hostname'];

    }

    public function getHostIp($name)
    {
        $hostip = $this->table('host')->field("INET6_NTOA(ip) ip")->join('left join host_ip on host.id = host_ip.host_id')->where(['hostname'=>$name])->find();
        return $hostip['ip'];
    }

}

?>