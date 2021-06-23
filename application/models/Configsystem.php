<?php

namespace App\Models;
use App\Models\Event;


class Configsystem extends Model
{
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';
    protected $server = 'localhost';

    protected $_validate1 = array(
        array('sensor_ip', 'checkHave', '采集IP不能为空！',1,'function',3),
        array('sensor_ip', 'checkIp', 'IP格式不正确', 1, 'function', 3),
    );

    protected $_validate2 = array(
        array('place', 'checkHave', '厂站信息不能为空！',1,'function',3),
        array('place', 'checkLength', '厂站信息不得超出于20字符', 1, 'function',3),
    );

    protected $_validate3 = array(
        array('server', 'checkHave', 'NTP对时IP不能为空！',1,'function',3),
        array('server', 'checkIp', 'NTP对时IP格式不正确', 1, 'function', 3),
    );

    protected $_validate13 = array(
        array('time', 'checkHave', '时间不能为空！',1,'function',3),
    );


    protected $_validate4 = array(
        array('ip', 'checkHave', '网关IP不能为空！',1,'function',3),
        array('ip', 'checkIp', '网关IP格式不正确', 1, 'function', 3),
    );

    protected $_validate5 = array(
        array('frameworkd_backup_storage_days_lifetime', 'checkHave', '要保留在文件系统中的备份文件数不能为空！',1,'function',3),
        array('frameworkd_backup_storage_days_lifetime', 'checkerInt', '要保留在文件系统中的备份文件数不是整数！', 1,'function',3),
        array('backup_day', 'checkHave', '要保存在数据库中的事件(天数)不能为空！',1,'function',3),
        array('backup_day', 'checkerInt', '要保存在数据库中的事件(天数)不是整数！', 1,'function',3),
        array('backup_events', 'checkHave', '要保存在数据库中的事件(事件数量)不能为空！',1,'function',3),
        array('backup_events', 'checkerInt', '要保存在数据库中的事件(事件数量)不是整数！', 1,'function',3),
        array('backup_netflow', 'checkHave', '活跃流量窗口不能为空！',1,'function',3),
        array('backup_netflow', 'checkerInt', '要保留在文件系统中的备份文件数不是整数！', 1,'function',3),
        array('alarms_lifetime', 'checkHave', '警报天数不能为空！',1,'function',3),
        array('alarms_lifetime', 'checkerInt', '要保留在文件系统中的备份文件数不是整数！', 1,'function',3),
    );

    protected $_validate6 = array(
        array('audit_db_threshold', 'checkHave', '审计日志备份阈值不能为空！',1,'function',3),
        array('audit_db_threshold', 'checkerInt', '审计日志备份阈值不是整数！', 1,'function',3),
    );

    protected $_validate7 = array(
        array('host', 'checkHave', '事件转发服务器地址不能为空！',1,'function',3),
        array('host', 'checkIp', '事件转发服务器地址格式不正确', 1, 'function', 3),
        array('port', 'checkHave', '事件转发端口不能为空！',1,'function',3),
        array('port', 'checkerInt', '事件转发端口不是整数！', 1,'function',3),
    );

    protected $_validate8 = array(
        array('host', 'checkHave', '告警转发服务器地址不能为空！',1,'function',3),
        array('host', 'checkIp', '告警转发服务器地址格式不正确', 1, 'function', 3),
        array('port', 'checkHave', '告警转发服务器地址不能为空！',1,'function',3),
        array('port', 'checkerInt', '告警转发端口不是整数！', 1,'function',3),
    );

    protected $_validate9 = array(
        array('destination', 'checkHave', '目的网段地址格式不正确！',1,'function',3),
        array('destination', 'checkIp', '目的网段地址格式不正确', 1, 'function', 3),
        array('genmask', 'checkHave', '目的网段子网掩码不能为空！',1,'function',3),
        array('genmask', 'checkIp', '目的网段子网掩码格式不正确', 1, 'function', 3),
        array('gateway', 'checkHave', '路由网关IP地址不能为空！',1,'function',3),
        array('gateway', 'checkIp', '路由网关IP地址格式不正确', 1, 'function', 3),
    );

    protected $_validate10 = array(
        array('custom_host_ip', 'checkHave', '管理平台IP地址不能为空！',1,'function',3),
        array('custom_host_ip', 'checkIp', '管理平台IP地址格式不正确', 1, 'function', 3),
    );

    protected $_validate11 = array(
        array('ip', 'checkHave', 'IP地址不能为空！',1,'function',3),
        array('ip', 'checkIp', 'IP地址格式不正确', 1, 'function', 3),
        array('mask', 'checkHave', '掩码不能为空！',1,'function',3),
        array('mask', 'checkIp', '掩码格式不正确', 1, 'function', 3),
    );

    protected $_validate12 = array(
        array('threat_host', 'checkHave', '情报服务器IP不能为空！',1,'function',3),
        array('threat_host', 'checkIp', '情报服务器IP地址格式不正确', 1, 'function', 3),
        array('threat_host_port', 'checkHave', '情报服务器端口不能为空！',1,'function',3),
        array('threat_host_port', 'checkerInt', '要保留在文件系统中的备份文件数不是整数！', 1,'function',3),
    );

    protected $_validate14 = array(
        array('time_out', 'checkerInt', '登录超时配置不是整数！', 1,'function',3),
        array('fail_count', 'checkerInt', '登录失败次数不是整数！', 1,'function',3),
        array('fail_time', 'checkerInt', '登录失败时长不是整数！', 1,'function',3),
    );

    protected $_validate15 = array(
        array('strategy_num', 'checkerInt', '数量不是整数！', 1,'function',3),
    );
    /*
   * 安全日志备份
   *
   */
    public function getLogBackup()
    {

        $where = ['frameworkd_backup_storage_days_lifetime', 'backup_day', 'backup_events', 'backup_hour', 'backup_netflow',
            'alarms_lifetime', 'backup_store', 'backup_events_min_free_disk_space', 'alarms_expire'];
        $data = $this->where(['conf' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    /*
     * 安全备份设置
     *
     */
    public function setLogBackup($param)
    {
        $this->_validate = $this->_validate5;
        if (!$this->create()) {
            $errtips = $this->getError();
            if (!empty($errtips)) {
                echo  $errtips;
            }
        }

        return $this->operateService($param);
    }

    /*
    * 审计日志备份
    *
    */
    public function getAuditBackup()
    {
        $where = ['audit_db_threshold', 'audit_space_threshold', 'audit_db_set'];
        $data = $this->where(['conf' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    /*
     *审计日志备份设置
     * */
    public function setAuditBackup($param)
    {
        $this->_validate = $this->_validate6;
        if (!$this->create()) {
            $errtips = $this->getError();
            if (!empty($errtips)) {
                return $errtips;
            }
        }

        return $this->operateService($param);
    }

    /*
   * 获取集团管理平台设置
   *
   */
    public function getPlatformIp()
    {
        $where = ['custom_host_ip'];
        $data = $this->field('*')->where(['conf' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }


    /*
     *  设置集团管理平台设置
     * */
    public function setPlatformIp($custom_host_ip)
    {
        $this->_validate = $this->_validate10;
        if (!$this->create()) {
            $errtips = $this->getError();
            if (!empty($errtips)) {
                return $errtips;
            }
        }

        return $this->operateService($custom_host_ip);
    }


    /*
     * 获取访问平台的ip列表
     * */
    public function getRequestList()
    {
        $where = ['can_request_ip'];
        $data = $this->where(['conf' => ['in', $where]])->find();
        if (!empty($data['value'])) {
            $data = explode('-', $data['value']);
            $res = [];
            foreach ($data as $k => $v) {
                $arr = explode('/', $v);
                $res[$k]['ip'] = $arr[0];
                $res[$k]['mask'] = $arr[1];
            }
            foreach ($res as $key =>$val ){
               if($val['ip'] == '19.19.19.19'){
                    unset($res[$key]);
               }
            }
            return array_values($res);
        }

    }


    //删除访问的ip
    public function delRequestList($param)
    {
        $str = $param['ip'] . '/' . $param['mask'];
        $where = ['can_request_ip'];
        $data = $this->where(['conf' => ['in', $where]])->find();
        $datas = explode('-', $data['value']);
        foreach ($datas as $k => $v) {

            if ($str == $v) {
                unset($datas[$k]);
            }
        }
        $new_str = implode('-', $datas);
        $result = $this->where(['conf' => 'can_request_ip'])->save(['value' => $new_str]);
        if ($result < 0) {
            return false;
        }
        return true;
    }


    /*
     * 启动ip限制
     * */
    public function startIpLimit($param)
    {
        $result = $this->where(['conf' => 'enable_request_ip'])->save(['value' => $param]);

        if ($result < 0) {
            return false;
        }
        return true;
    }


    /*
    * 允许访问平台的Ip设置
     * */
    public function setRequestIp($param)
    {
        $this->_validate = $this->_validate11;
        if (!$this->create()) {
            $errtips = $this->getError();
            if (!empty($errtips)) {
                return $errtips;
            }
        }

        $str = $param['ip'] . '/' . $param['mask'];
        $where = ['can_request_ip'];
        $data = $this->field('value')->where(['conf' => ['in', $where]])->find();

        foreach (explode('-', $data['value']) as $k => $v) {
            $ip_infor = explode('/', $v);
            $ips[] = $ip_infor[0];
        }

        if (in_array($param['ip'], $ips) === true) {
            return 'IP已存在！';
            die;
        }
        if (!empty($data['value'])) {
            $new_str = $data['value'] . '-' . $str;

        } else {
            $new_str = $str;
        }
        $result = $this->where(['conf' => 'can_request_ip'])->save(['value' => $new_str]);
        if ($result < 0) {
            return false;
        }
        return true;

    }

    //获取ip显示状态
    public function getIpLimit()
    {
        $where = ['enable_request_ip'];
        $data = $this->field('value')->where(['conf' => ['in', $where]])->find();
        return $data;
    }


//    /*
//     * 允许访问平台的Ip设置
//     * */
//    public function setRequestIp($can_request_ip)
//    {
//        $can_request_ips = $this->field('value')->where(['conf' => 'can_request_ip'])->find();
//        // var_dump($can_request_ips);
//        if (!empty($can_request_ips['value'])) {
//            $ips = explode('-', $can_request_ips['value']);
//            if (in_array($can_request_ip, $ips) === false) {
//                if (($this->field('value')->where(['conf' => 'can_request_ip'])->save(['value' => $can_request_ips['value'] . '-' . $can_request_ip])) > 0) {
//                    return true;
//                } else {
//                    return false;
//                }
//
//            } else {
//                return ['msg' => 'ip已设置'];
//            }
//        }
//    }

//    /*
//     * 删除已设置的IP
//     * */
//    public function delRequestIp($can_request_ip)
//    {
//        $can_request_ips = $this->field('value')->where(['conf' => 'can_request_ip'])->find();
//        // var_dump($can_request_ips);
//        if (!empty($can_request_ips['value'])) {
//            $ips = explode('-', $can_request_ips['value']);
//            if (in_array($can_request_ip, $ips) === false) {
//                return ['msg' => 'ip不存在'];
//            } else {
//                foreach ($ips as $k => $v) {
//                    if ($v == $can_request_ip) {
//                        unset($ips[$k]);
//                    }
//                }
//                $ips = implode("-", $ips);
//                if (($this->field('value')->where(['conf' => 'can_request_ip'])->save(['value' => $ips])) > 0) {
//                    return true;
//                } else {
//                    return false;
//                }
//            }
//        }
//    }

    /*
     *  获取/设置网口配置
     * */
    public function getInterface($type, $param)
    {
        $config = [
            ['name' => 'ETH0',
                "ip" => "",
                "gateway" => "",
                "netmask" => "",
                "status" => ""
            ],
            ['name' => 'ETH1',
                "ip" => "",
                "gateway" => "",
                "netmask" => "",
                "status" => ""
            ],
            ['name' => 'ETH2',
                "ip" => "",
                "gateway" => "",
                "netmask" => "",
                "status" => ""
            ],
            ['name' => 'ETH3',
                "ip" => "",
                "gateway" => "",
                "netmask" => "",
                "status" => ""
            ],
            ['name' => 'ETH4',
                "ip" => "",
                "gateway" => "",
                "netmask" => "",
                "status" => ""
            ],
            ['name' => 'ETH5',
                "ip" => "",
                "gateway" => "",
                "netmask" => "",
                "status" => ""
            ]
        ];

        //  $config_eth = ['ETH0','ETH1','ETH2','ETH3','ETH4','ETH5'];
        if ($type == 'get_inter') {

            $data = $this->serverLink($this->server, 'get_interface');
            if (empty($data)) {
                return $config;
            } else {
//                foreach($data as $key => $val){
//                    $eth[] = $val['name'];
//                }
//                foreach($config_eth as $k => $v){
//                    if(!in_array($v,$eth)){
//                        $data[] = ['name' => $v,
//                            "ip" => "",
//                            "gateway" => "",
//                            "netmask" => "",
//                            "status" => ""
//                        ];
//                    }
//                }

                return json_decode($data, 255);
            }

        } elseif ($type === 'interface') {
            //为了实现 不是一个网段的不能设置
            $new_data = json_decode($param,1);
            $new_data = array_filter(array_column($new_data['data'],'ip'));
           // $arr = [];

//            foreach ($new_data as $k => $v){
//                preg_match('/\d+.\d+.(\d+).\d+/',$v,$rs);
//              //  var_dump($rs);
//                $arr[] = $rs[1];
//            }
//            //var_dump($arr);die;

            if(count($new_data) != count(array_unique($new_data))) return '不允许添加同一网段的IP！';

            return $this->serverLink($this->server, 'interface', '&name=' . $param);
        }

    }


    /*
    *  路由配置
    * */
    public function getRoute($type, $param)
    {
        if ($type == 'get_route') {

            return json_decode($this->serverLink($this->server, 'route1'), 255);
        } elseif ($type == 'add_route') {

            $this->_validate = $this->_validate9;
            if (!$this->create()) {

                $errtips = $this->getError();

                if (!empty($errtips)) {
                    return $errtips;
                }
            }

            return $this->serverLink($this->server, 'route2', '&destination=' . $param['destination'] . '&genmask=' . $param['genmask'] . '&gateway=' . $param['gateway'] . '&iname=' . $param['eth']);

        } elseif ($type == 'del_route') {
            return $this->serverLink($this->server, 'route3', '&destination=' . $param['destination'] . '&genmask=' . $param['genmask'] . '&gateway=' . $param['gateway'] . '&iname=' . $param['eth']);


        }

    }


    /*
     * 系统信息  ip
     * */
    public function getServiceIp()
    {
        return json_decode($this->serverLink($this->server, 'get_system_sensor_info'), 255);
    }

    /*
     * 获取系统信息网口
     *
     * */
    public function getInterfacePort()
    {
        $where = ['interface_port'];
        $data = $this->field('value')->where(['conf' => ['in', $where]])->find();
        return $data;
    }

    /*
   * 设置系统信息  ip
   * */
    public function setServiceIp($param)
    {
        $this->_validate = $this->_validate1;
        if (!$this->create()) {

            $errtips = $this->getError();
            if (!empty($errtips)) {
                return $errtips;
            }
        }
        return $this->serverLink($this->server, 'set_system_sensor_info', '&sensor_info=' . $param);
    }


    /*
      * 系统信息  id
     * */
    public function getServiceId()
    {
        return $this->serverLink($this->server, 'get_sensor_id');
    }


    /*
     * 获取厂站信息
     * */
    public function getServicePlace()
    {
        return $this->serverLink($this->server, 'get_sensor_place');
    }

    /*
     * 设置厂站信息
    * */
    public function setServicePlace($param)
    {
        $this->_validate = $this->_validate2;
        if (!$this->create()) {
            $errtips = $this->getError();
            if (!empty($errtips)) {
                return $errtips;
            }
        }

        return $this->serverLink($this->server, 'set_sensor_place', '&place=' . $param);
    }

    /*
   * 设置网口到数据库
  * */
    public function setIneterfacePort($param)
    {
        $arr['interface_port'] = $param;
        $result = $this->operateService($arr);
        if ($result === false) {
            return '失败';
        }
        return '成功';
    }


    /*
     * 系统ID刷新
     * */
    public function refresh()
    {
        return $this->serverLink($this->server, 'set_system_sensor_id');
    }


    /*
    *  事件转发
    * */
    public function Syslog($param)
    {

        if (isset($param['type']) && $param['type'] == 'get_syslog') {
            return json_decode($this->serverLink($this->server, 'get_syslog'), 255);

        } else {
            $this->_validate = $this->_validate7;
            if (!$this->create()) {
                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
            }

            return $this->serverLink($this->server, 'set_syslog', '&state=' . $param['state'] . "&host=" . $param['host'] . "&port=" . $param['port']);
        }

    }

    /*
    *  告警转发
     * */
    public function AlarmSyslog($param)
    {
        if (isset($param['type']) && $param['type'] == 'get_alarm_syslog') {

            return json_decode($this->serverLink($this->server, 'get_alarm_syslog'), 255);

        } else {

            $this->_validate = $this->_validate8;
            if (!$this->create()) {

                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
            }

            return $this->serverLink($this->server, 'set_alarm_syslog', '&state=' . $param['state'] . "&host=" . $param['host'] . "&port=" . $param['port'] . "&origin=" . $param['port']);
        }

    }

    /*
     * 流量配置
     * */
    public function traffic($type, $param)
    {
        if ($type == 'get_flow') {
            // $a = '{"error_code":0,"error_msg":"","data":{"type":2,"port":0,"device":"eth5","threat_host":"","threat_host_port":0,"es_threshold":"","es_auto_backup":0}}';
            // $b = json_decode($a, 255);
            // return $b['data'];
            $data = json_decode($this->serverLink($this->server, 'get_flow_analysis'), 255);
            return $data['data'];
        } elseif ($type = 'set_flow_analysis') {

            $this->_validate = $this->_validate12;

            if (!$this->create()) {
                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
            }

            $param['data']['type'] = intval($param['data']['type']);
            $param['data']['port'] = intval($param['data']['port']);
            $param['data']['threat_host_port'] = intval($param['data']['threat_host_port']);
            $param['data']['es_auto_backup'] = intval($param['data']['es_auto_backup']);
            $param['data']['es_threshold'] = intval($param['data']['es_threshold']);
            $res = json_encode($param['data']);
            return $this->serverLink($this->server, 'set_flow_analysis', '&config=' . $res);
        }
    }


    /*
     * 获取系统版本
     * */

    public function getVersion()
    {
        exec("cat /version.txt", $version);

        return $version[0];
    }

    /*
    *  系统对时
    *  */
    public function getSystemTime()
    {
        $res = json_decode($this->serverLink($this->server, 'get_system_time'), 255);
        $data['serverTime'] = $res['time'];
        $data['ch'] = '1';
        $data['server'] = $res['server'];
        return $data;

    }

    /*
     * 设置系统对时
        * */

    public function setSystemTime($param)
    {
        $str = substr($param['time'], 0, 10) . '_' . substr($param['time'], 11);
        if (isset($param['time']) && $param['ch'] == 0) {

            $this->_validate = $this->_validate13;

            if (!$this->create()) {

                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
            }
            return $this->serverLink($this->server, 'set_system_time', '&time=' . $str . '&server=&ch=0&rate=per_hour');
        } elseif (isset($param['server']) && !empty($param['server']) && $param['ch'] == 1) {
            $this->_validate = $this->_validate3;
            if (!$this->create()) {
                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
            }

            return $this->serverLink($this->server, 'set_system_time', '&time=&server=' . $param['server'] . '&ch=1&rate=per_hour');
        }

    }


    /*
    *  系统测试
    * */
    public function ping($param)
    {
        return $this->serverLink($this->server, $param['type'], '&server=' . $param['server']);
    }


    /*
     * 安全认证网关
     * */
    public function safeCertificate($param = '')
    {
        if ($param != '') {
            $this->_validate = $this->_validate4;
            if (!$this->create()) {
                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
            }
            return $this->serverLink($this->server, 'set_ukey_ip', '&ip=' . $param);
        }

        $res = json_decode($this->serverLink($this->server, 'get_ukey_ip'), 255);
        //用小写 前端接不到
        $res['ip'] = $res['IP'];
        unset($res['IP']);
        return $res;
    }


    /*
     * 系统升级
     * */
    public function upgrade($param)
    {
        return $this->serverLink($this->server, 'upgrade', '&file=' . $param);
    }


    /*
     * 系统关机
     * */
    public function shutdown($param)
    {
        return $this->serverLink($this->server, $param['type']);
    }

    /*
    * 系统重启
    * */
    public function restart($param)
    {
        return $this->serverLink($this->server, $param['type']);
    }


    /*
     * 访问服务
     * */
    public function serverLink($server, $type, $param = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://' . $server . ':8080/interface/edit?type=' . $type . $param);
        //echo 'http://' . $server . ':8080/interface/edit?type=' . $type . $param;die;
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        //echo $data;
        curl_close($curl);
        return $data;
    }

//config的添加或者更新
    public function operateService($param)
    {
        $tag = true;
        foreach ($param as $k => $v) {
            if ($this->where(['conf' => $k])->count()) {
                //这里是更新
                $data = ['value' => $v];
                $where = ['conf' => $k];
                $result = $this->where($where)->save($data);

                if ($result === false) {
                    $tag = false;
                    break;
                }
            } else {
                //添加
                $data = ['conf' => $k, 'value' => $v];
                $result = $this->add($data);
                if ($result === false) {

                    $tag = false;
                    break;
                }
            }

        }
        return $tag;
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

    //修改管理口IP
    public function editManagementIp($data)
    {

        /*
            1、修改mysql关联
        */
        $OLD_IP = $this->table('system')->where(['name' => 'alienvault'])->getField('inet6_ntoa(admin_ip) ip');

        //数据源
        $sqls = [
            "update alienvault.config set value = '{$data["ip"]}' WHERE conf = 'frameworkd_address'",
            "replace into alienvault.config values('frameworkd_eth','{$data["eth"]}')",
            "replace into alienvault.config values('frameworkd_mask','{$data["mask"]}')",
            "replace into alienvault.config values('frameworkd_gateway','{$data["gateway"]}')",

            "update alienvault.config set value = '{$data["ip"]}' WHERE conf = 'nessus_host'",
            "update alienvault.config set value = '127.0.0.1' WHERE conf = 'server_address'",
            "update alienvault.config set value = 'yes' WHERE conf = 'server_alarms_to_syslog'",
            "update alienvault.config set value = '127.0.0.1' WHERE conf = 'backup_host'",

            "update alienvault.server set ip = INET6_ATON('{$data["ip"]}') where name = 'alienvault'",
            "update alienvault.server_role set alienvault.server_role.alarms_to_syslog = 1",

            "update alienvault.system set alienvault.system.admin_ip = INET6_ATON('{$data["ip"]}') where INET6_NTOA(alienvault.system.admin_ip) = '$OLD_IP'",
            "update alienvault.sensor set alienvault.sensor.ip= INET6_ATON('{$data["ip"]}') where INET6_NTOA(alienvault.sensor.ip) = '$OLD_IP'",
            "update alienvault.host_ip set alienvault.host_ip.ip = INET6_ATON('{$data["ip"]}') where INET6_NTOA(alienvault.host_ip.ip) = '$OLD_IP'",
        ];

        $this->startTrans();

        //循环执行
        foreach ($sqls as $sql) {
            $pool[] = $this->execute($sql);
        }
        //判断事务状态
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();
            jsonError($sqls);
        } else {
            $this->commit();
        }

        /*
            2.向接口发起请求
        */
        $str = APP_PATH . "/outside/modify_configure.py {$data['ip']}";
        $str = `python {$str}`;

        return $str;
    }

    //获得管理口IP
    public function getManagementIp()
    {
        $where = [
            'conf' => ['in', ['frameworkd_address', 'frameworkd_eth', 'frameworkd_mask', 'frameworkd_gateway']],
        ];

        $res = $this->table('config')->where($where)->select();
        $res1 = array_combine(array_column($res, 'conf'), array_column($res, 'value'));
        $result = [];
        $result['eth'] = \Tools::isEmpty($res1['frameworkd_eth']) ? '' : $res1['frameworkd_eth'];
        $result['ip'] = \Tools::isEmpty($res1['frameworkd_address']) ? '' : $res1['frameworkd_address'];
        $result['mask'] = \Tools::isEmpty($res1['frameworkd_mask']) ? '' : $res1['frameworkd_mask'];
        $result['gateway'] = \Tools::isEmpty($res1['frameworkd_gateway']) ? '' : $res1['frameworkd_gateway'];
        return $result;
    }


    //超时
    public function getTimeOut()
    {
        $where = ['time_out'];
        $data = $this->where(['conf' => ['in', $where]])->select();
        return $this->dataDispost($data);
    }

    //失败次数
    public function getFailCount()
    {
        $where = ['fail_count'];
        $data = $this->where(['conf' => ['in', $where]])->select();
        return $this->dataDispost($data);
    }

    //锁定时间
    public function getFailTime()
    {
        $where = ['fail_time'];
        $data = $this->where(['conf' => ['in', $where]])->select();
        return $this->dataDispost($data);
    }

    //登录时获取的
    public function getTimeOutFailCount()
    {
        $where = ['time_out','fail_count','fail_time'];
        $data = $this->where(['conf' => ['in', $where]])->select();
        return $this->dataDispost($data);
    }


    /*
     *  设置超时时间、失败次数、锁定时间
     * */
    public function setTimeConfig($params)
    {
        $this->_validate = $this->_validate14;
        if (!$this->create()) {
            $errtips = $this->getError();
            if (!empty($errtips)) {
                return $errtips;
            }
        }

        return $this->operateService($params);
    }

    public function getStrategy()
    {
        $where = ['strategy_num','strategy_time'];
        $data = $this->field('*')->where(['conf' => ['in', $where]])->select();
        return $this->dataDispost($data);
    }

    public function setStrategy($data)
    {
        $this->_validate = $this->_validate15;
        if (!$this->create()) {
            $errtips = $this->getError();
            if (!empty($errtips)) {
                return $errtips;
            }
        }
        return $this->operateService($data);
    }

    public function getStrategyIps()
    {
        $where = ['strategy_ips'];
        $str = $this->field('value')->where(['conf' => ['in', $where]])->find();
        if(!empty($str['value'])){
            $data = explode('-',$str['value']);
            $arr = [];
            $model = new Event();
            foreach ($data as $k=> $v){
                $da = $model->getSrcInfo($v);
                $name = $model->getDeviceIp($da['device_id']);
                $arr[$k]['address'] = $v;
                $arr[$k]['proto'] = $model->getPro($da['ip_proto']);
                $arr[$k]['number'] = $da['layer4_sport'];
                $arr[$k]['ip_port'] = $name['hostname'];
                $arr[$k]['sensor'] = $name['device_ip'];
            }
            return $arr;
        }
    }

    public function updateStrategyIP($ips)
    {
        return $this->operateService($ips);
    }
}

?>