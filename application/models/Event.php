<?php

namespace App\Models;

class Event extends Model
{
    protected $dbName = 'alienvault_siem';
    protected $tableName = 'acid_event';
    protected $tablePrefix = '';
    protected $pk = 'id';

    public function getCount($where)
    {
        $where = self::getWhere($where);
        if($_SESSION['user_device_power'] != 'all' && $_SESSION['user_monitor_power'] != 'all' && empty($Adevice)){

            $str = [];
            if(!empty($_SESSION['user_device_power'])){
                $user_device_power = array_shift(json_decode($_SESSION['user_device_power'],1));
                if(!empty($user_device_power['device_id'])){
                    array_push($str,$user_device_power['device_id']);
                }


            }
            if(!empty($_SESSION['user_monitor_power'])){
                $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'],1));
                if(!empty($user_monitor_power['device_id'])){
                    array_push($str,$user_monitor_power['device_id']);
                }

            }

            $sstr = implode(',',$str);

            $where[] = ['a.device_id' => ["exp", "in ({$sstr})"]];
            $count = $this->alias('a')->where($where)->count('id');

        }else{
            $count = $this->alias('a')->where($where)->count('id');
       }

        return $count;
    }

    private function getWhere($where)
    {

        if (!empty($where['begindate']) && !empty($where['enddate'])) {

            //$new_where['a.TIMESTAMP'] = ['between',[$where['begindate'],$where['enddate']]];

            $new_where[] = ['a.TIMESTAMP' => ["exp", "between '{$where['begindate']}' and '{$where['enddate']}'"]];
        }

        //如果有名字  去查主子插件id
        if (!empty($where['eventname'])) {

            $data = explode('-', $where['eventname']);

            //  $data = $this->table('plugin_sid')->field('plugin_id,sid')->where(['name' => "{$where['eventname']}"])->find();
            $new_where[] = [['a.plugin_id' => ["exp", "={$data[0]}"]],
                ['a.plugin_sid' => ["exp", "={$data[1]}"]],
            ];
        }

        if (strlen($where['risk']) > 0) {
            //  $new_where['a.risk_a'] = $where['risk'];
            $new_where[] = ['a.ossim_risk_a' => ["exp", "={$where['risk']}"]];
        }

        if (!empty($where['protocol'])) {
            //$new_where['a.protocol'] = $where['protocol'];
            $new_where[] = ['a.ip_proto' => ["exp", "={$where['protocol']}"]];
        }

        if (!empty($where['src_hostname']) && empty($where['src_ip']) ) {
            $ip = self::getHostIp($where['src_hostname']);
            $new_where[] = ['a.ip_src' => ["exp", "= INET6_ATON('{$ip}')"]];
        }
        if (!empty($where['dst_hostname']) && empty($where['dst_ip']) ) {
            $ip = self::getHostIp($where['dst_hostname']);
            $new_where[] = ['a.ip_dst' => ["exp", "= INET6_ATON('{$ip}')"]];
        }
        if (!empty($where['src_ip']) && empty($where['src_hostname'])) {
            $new_where[] = ['a.ip_src' => ["exp", "= INET6_ATON('{$where['src_ip']}')"]];
        }

        if (!empty($where['dst_ip']) && empty($where['dst_hostname'])) {
            $new_where[] = ['a.ip_dst' => ["exp", "= INET6_ATON('{$where['dst_ip']}')"]];
        }

        if(!empty($where['src_ip']) && !empty($where['src_hostname'])){
            $new_where[] = ['a.ip_src' => ["exp", "= INET6_ATON('{$where['src_ip']}')"]];
        }

        if(!empty($where['dst_ip']) && !empty($where['dst_hostname'])){
            $new_where[] = ['a.ip_dst' => ["exp", "= INET6_ATON('{$where['dst_ip']}')"]];
        }

        if (!empty($where['src_port'])) {
            $new_where[] = ['a.layer4_sport' => ["exp", "={$where['src_port']}"]];
        }

        if (!empty($where['dst_port'])) {
            $new_where[] = ['a.layer4_dport' => ["exp", "={$where['dst_port']}"]];
        }

//        if (!empty($where['interface'])) {
//            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
//            $new_where[] = ['a.interface' => ["exp", "='{$where['interface']}'"]];
//        }

        if (!empty($where['plugin_id'])) {
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            $new_where[] = ['a.plugin_id' => ["exp", "={$where['plugin_id']}"]];
        }

        if (!empty($where['plugin_sid'])) {
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            $new_where[] = ['a.plugin_sid' => ["exp", "={$where['plugin_sid']}"]];
        }

        //几区采集器搜索
        if(!empty($where['sensor'])){
            $where = ['device_ip' => ["exp", " = INET6_ATON('{$where['sensor']}')"]];

            //这里有问题  device表 一个采集器 对象一个数据 这里 ip相同 sensor_id不同的有好几条数据  导致有好几个主键id
            //你拿获取到的第一个id可能查不到数据  同时 事件表的device_id也有问题  不同的数字 指向同意个采集器 同意个采集器ip
            //所以这里为了数据展示 将等于换成 in范围去查

            $ids = $this->table('alienvault_siem.device')->field('id')->where($where)->select();

///这里注释掉 因为中烟项目里 device表的数据又不相同了   $ids只有一条数据 直接安全事件的device_id 去devicec表查的ip 就是采集器ip
             if(!empty($ids)){
                 $arr  = [];
                 foreach ($ids as $k => $v){
                     array_push($arr,$v['id']);
                 }
                 $str = implode(',',$arr);

             }

            $new_where[] = ['a.device_id' => ["exp", "in ({$str})"]];
           // $new_where[] = ['a.device_id' => ["exp", "in ({$ids[0]['id']})"]];
        }

        return $new_where;
    }

    public function getDataList($field, $join = "", $where, $group = "", $order = "", $page, $page_size)
    {
        //xss过滤事件
        foreach($where as $k => $v) $where[$k] = self::xss_filter($v);

        $where = self::getWhere($where);

        $Adevice = array_column($where,"a.device_id");

        if($_SESSION['user_device_power'] != 'all' && $_SESSION['user_monitor_power'] != 'all' && empty($Adevice)){

            $str = [];
            if(!empty($_SESSION['user_device_power'])){
                $user_device_power = array_shift(json_decode($_SESSION['user_device_power'],1));
                if(!empty($user_device_power['device_id'])){
                    array_push($str,$user_device_power['device_id']);
                }

            }
            if(!empty($_SESSION['user_monitor_power'])){
                $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'],1));
                if(!empty($user_monitor_power['device_id'])){
                    array_push($str,$user_monitor_power['device_id']);
                }
            }

            $sstr = implode(',',$str);
            $where[] = ['a.device_id' => ["exp", "in ({$sstr})"]];
        }

        $data = $this->field($field)->alias("a")
            ->join($join)->where($where)->group($group)->order($order)->page($page, $page_size)->select();

        return $data;

    }

    public function getOneStats($field, $join = '', $where, $group)
    {
        $data = $this->alias('a')->field($field)->join($join)->where($where)->group($group)->find();
        //echo $this->getlastsql();die;
        return $data;
    }

    //plugin_sid 获取事件名称
    public function getEventName($param)
    {
        $data = $this->table('alienvault.plugin_sid')->field('name')->where($param)->find();

        return $data['name'];
    }

//    //获取所属资产名
//    public function getHoseName($param)
//    {
//        $data = $this->table('host')->field('host.hostname')->join('host_ip on host_ip.host_id = host.id ')
//            ->join('sensor on sensor.ip = host_ip.ip ')->where(['sensor.id' => ["exp", "=unhex('{$param}')"]])->find();
//        return $data['hostname'];
//    }
    /**
     * 根据条件检索事件
     */
    public function getAll($field, $where = [], $order = 'a.timestamp desc')
    {
        /* $join = 'alienvault.plugin_sid b ON a.plugin_sid = b.sid';
         $group = 'a.id';
         $result = $this->field($field)->table('alienvault.event')->alias('a')->join('alienvault.plugin_sid b ON a.plugin_sid = b.sid','LEFT')->where($where)->group($group)->order($order)->select();
         return $result;*/
        $page = 0;
        $page_size = 100;

        $result = $this->field($field)->alias('a')->where($where)->order($order)->page($page, $page_size)->select();
        //exit($this->_sql());
        /*
         获得附加属性
        */
        if (!\Tools::isEmpty($result)) {
            array_walk($result, function (&$arr) {
                $where = ['sid' => $arr['plugin_sid'],'plugin_id' => $arr['plugin_id']];
                $arr['event_name'] = $this->getEventName($where);
            });
        }
        return $result;
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
        $id = $this->field('plugin_id,plugin_sid')->group('plugin_id,plugin_sid')->select();
        $data = [];
        foreach ($id as $k => $v) {
            $r = self::getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            if(!empty($r)){
                $data['names'][] = $r;
                $data['ids'][] = $v['plugin_id'] . '-' . $v['plugin_sid'];
            }
        }
        $res['ids'] = implode(',', $data['ids']);
        $res['names'] = implode(',', $data['names']);
        return $res;
    }

    public function pro()
    {
        $ids = $this->field('ip_proto')->group('ip_proto')->select();

        $where['number'] = ['in', array_column($ids, 'ip_proto')];
        $protocol = $this->table('protocol')->field('number,name as protocol')->where($where)->select();
        $data = [];
        foreach ($protocol as $k => $v) {
            $data['number'][] = $v['number'];
            $data['protocol'][] = $v['protocol'];
        }
        $res['number'] = implode(',', $data['number']);
        $res['protocol'] = implode(',', $data['protocol']);
        return $res;
    }

    public function getExpand($id)
    {
        $where = ['event_id' => ["exp", " = unhex('{$id}')"]];
        return $this->table('alienvault_siem.extra_data')->field("userdata1,userdata2,userdata3,userdata4,userdata5,userdata6,userdata7,userdata8,userdata9,data_payload")->where($where)->find();
    }

    public function sensor()
    {
        if($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all'){
            $sensor = $this->table('alienvault.udp_sensor')->field('ip,name')->select();
            $data = [];
            foreach ($sensor as $k => $v) {
                $data['ip'][] = $v['ip'];
                $data['name'][] = $v['name'];
            }
            $res['ip'] = implode(',', $data['ip']);
            $res['name'] = implode(',', $data['name']);

        }else{
            $name = [];
            $ip = [];
            if(!empty($_SESSION['user_device_power'])){
                $user_device_power = array_shift(json_decode($_SESSION['user_device_power'],1));
                $sensor_name = $this->table('alienvault.udp_sensor')->field('name')->where(['ip' => $user_device_power['device_ip']])->find();
                array_push($name,$sensor_name['name']);
                array_push($ip,$user_device_power['device_ip']);
            }

            if(!empty($_SESSION['user_monitor_power'])){
                $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'],1));
                $shenji_name = $this->table('alienvault.udp_sensor')->field('name')->where(['ip' => $user_monitor_power['device_ip']])->find();
                array_push($name,$shenji_name['name']);
                array_push($ip,$user_monitor_power['device_ip']);
            }

            $res['ip'] = implode(',',$ip);
            $res['name'] = implode(',', $name);

        }
        return $res;
    }

    public function getDeviceIp($device_id)
    {
        $device_ip = $this->table('alienvault_siem.device')->field('INET6_NTOA(device_ip) as device_ip')->where(['id' => $device_id])->find();
        $device_ip_hostname = $this->table('alienvault.host_ip a')->field('b.hostname')->join("left join alienvault.host b on a.host_id = b.id")->where(['a.ip' => ["exp", "= INET6_ATON('{$device_ip['device_ip']}')"]])->find();
        $device_ip['hostname'] = $device_ip_hostname['hostname'];
        return $device_ip;
    }

    public function getHostName($ip)
    {
        $hostname = $this->table('host')->field('hostname')->join('left join host_ip on host.id = host_ip.host_id')->where(['ip'=>["exp","=INET6_ATON('{$ip}')"]])->find();
        return $hostname['hostname'];
    }
    /**
    * 根据资产IP查出资产名称
    */
    public function ipToHostname($ip)
    {
        $hash = 'table-ip-hostname';
        $hostname  = $this->redis->hashGet($hash,$ip);
        //return ($hostname == false) ? ('未命名IP'.$ip) : $hostname;
        return ($hostname == false) ? ($ip) : $hostname;
    }


    public function getHostIp($name)
    {
        $hostip = $this->table('host')->field("INET6_NTOA(ip) ip")->join('left join host_ip on host.id = host_ip.host_id')->where(['hostname'=>$name])->find();
        return $hostip['ip'];
    }

    public function getEventSensorId($id)
    {
        $sensor_id = $this->alias('a')->field('hex(b.sensor_id) as sensor_id')->join('left join alienvault_siem.device b on b.id = a.device_id')->where(['b.id' =>$id])->find();
        return $sensor_id['sensor_id'];
    }

    public function getSrcInfo($ip)
    {
        $data =  $this->field("ip_proto,layer4_sport,device_id")->where(["ip_src" => ['exp'," =INET6_ATON('{$ip}')"]])->find();
        return $data;
    }
}

?>
