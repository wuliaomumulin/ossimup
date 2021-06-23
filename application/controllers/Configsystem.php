<?php

use App\Models\Configsystem;
use App\Models\UdpSensor;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class ConfigsystemController extends Base
{

    protected $Configsystem = '';
    protected $num = 23;

    public function init()
    {
        parent::init();
        $this->Configsystem = new Configsystem();
        $this->model = new UdpSensor();
       $this->checkAuth($this->num);
    }


    //安全日志备份
    public function getLogBackupAction()
    {
        $data = $this->Configsystem->getLogBackup();
        $res = encryptcode(json_encode($data, 1));
        jsonResult($res);
    }


    //安全备份日志设置
    public function setLogBackupAction()
    {
        $data['alarms_expire'] = decryptcode(input('post.alarms_expire'));
        $data['alarms_lifetime'] = decryptcode(input('post.alarms_lifetime'));
        $data['backup_day'] = decryptcode(input('post.backup_day'));
        $data['backup_events'] = decryptcode(input('post.backup_events'));
        $data['backup_events_min_free_disk_space'] = decryptcode(input('post.backup_events_min_free_disk_space'));
        $data['backup_hour'] = decryptcode(input('post.backup_hour'));
        $data['backup_netflow'] = decryptcode(input('post.backup_netflow'));
        $data['backup_store'] = decryptcode(input('post.backup_store'));
        $data['frameworkd_backup_storage_days_lifetime'] = decryptcode(input('post.frameworkd_backup_storage_days_lifetime'));

        if (!\Tools::isEmpty($data)) {
            $data['res'] = $this->Configsystem->setLogBackup($data);
            if ($data['res'] == '成功') {
                $this->logger(61);
            } else {
                $this->logger(62);
            }

            jsonResult($data);
        }

    }


    //审计日志备份
    public function getAuditBackupAction()
    {
        $data = $this->Configsystem->getAuditBackup();
        $res = encryptcode(json_encode($data, 1));
        jsonResult($res);
    }


    //审计日志备份设置
    public function setAuditBackupAction()
    {
        $data['audit_db_set'] = decryptcode(input('post.audit_db_set'));
        $data['audit_db_threshold'] = decryptcode(input('post.audit_db_threshold'));
        $data['audit_space_threshold'] = decryptcode(input('post.audit_space_threshold'));
        if (!\Tools::isEmpty($data)) {
            $data['res'] = $this->Configsystem->setAuditBackup($data);
            if ($data['res'] == '成功') {
                $this->logger(63);
            } else {
                $this->logger(64);
            }
            jsonResult($data);
        }
    }


    //集团管理平台设置
    public function platformIpAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            //post传的
            $info['custom_host_ip'] = decryptcode(input('post.custom_host_ip'));
            $data['res'] = $this->Configsystem->setPlatformIp($info);
            if ($data['res'] === true) {
                $this->logger(73);
            } else {
                $this->logger(74);
            }
            jsonResult($data);
        } else {
            $data = $this->Configsystem->getPlatformIp();
            $res = encryptcode(json_encode($data, 1));
            jsonResult($res);
        }

    }

    //获取访问平台的ip列表
    public function getRequestListAction()
    {
        $data = $this->Configsystem->getRequestList();
        $res = encryptcode(json_encode($data, 1));
        jsonResult($res);
    }

    //访问平台的ip删除
    public function delRequestListAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            $info['ip'] = decryptcode(input('post.ip'));
            $info['mask'] = decryptcode(input('post.mask'));
            $data = $this->Configsystem->delRequestList($info);
            if ($data === true) {
                $this->logger(75);
            } else {
                $this->logger(76);
            }
            jsonResult($data);
        }

    }

    //允许访问平台的ip设置
    public function setRequestIpAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            $info['ip'] = decryptcode(input('post.ip'));
            $info['mask'] = decryptcode(input('post.mask'));
            $data = $this->Configsystem->setRequestIp($info);
            if ($data === true) {
                $this->logger(77);
                jsonResult($data['status'] = 1);
            } elseif ($data === false) {
                $this->logger(78);
                jsonResult($data['status'] = 0);
            } else {
                jsonError($data);
            }

        }
    }


    //是否启动ip限制设置
    public function startIpLimitAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            $enable = decryptcode(input('post.enable'));
            $data = $this->Configsystem->startIpLimit($enable);
            jsonResult($data);
        }
    }

    //获取是否启动ip限制
    public function getIpLimitAction()
    {
        $data = $this->Configsystem->getIpLimit();
        jsonResult($data);
    }

//    //允许访问平台的ip设置
//    public function RequestIpAction()
//    {
//        $can_request_ip = input('post.can_request_ip/s');
//        $canRequestIP = input('get.can_request_ip/s');
//        if (!\Tools::isEmpty($can_request_ip)) {
//            $data['res'] = $this->Configsystem->setRequestIp($can_request_ip);
//            jsonResult($data);
//        }
//        if (!\Tools::isEmpty($canRequestIP)) {
//            $data = $this->Configsystem->delRequestIp($canRequestIP);
//            jsonResult($data);
//        }
//    }


    //网口配置/设置
    public function interfaceAction()
    {
        $type = input('get.type') ? input('get.type') : input('post.type');

        $data['data'] = json_decode(decryptcode(input('post.interfaceList')), 256);

        foreach ($data['data'] as $k => $v) {

            if ((!empty($v['netmask']) || !empty($v['gateway'])) || (!empty($v['netmask']) && !empty($v['gateway']))) {
                if (empty($v['ip'])) {
                    jsonError('IP不能为空！');
                }
            }
        }

        $params = json_encode($data, 1);

        //$param = '{"data":[{"name":"lo","ip":"","netmask":"","gateway":""},{"name":"eth0","ip":"","netmask":"","gateway":""},{"name":"eth1","ip":"","netmask":"","gateway":""},{"name":"eth2","ip":"","netmask":"","gateway":""},{"name":"eth3","ip":"10.163.1.18","netmask":"255.255.255.240","gateway":""},{"name":"eth4","ip":"","netmask":"","gateway":""},{"name":"eth5","ip":"19.19.19.19","netmask":"255.255.255.0","gateway":""},{"name":"docker0","ip":"","netmask":"","gateway":""}]}';
        if (!\Tools::isEmpty($type)) {
            $type = decryptcode($type);

            $data = $this->Configsystem->getInterface($type, $params);
            if (is_array($data)) {
                $res = encryptcode(json_encode($data, 1));
                jsonResult($res);
            } elseif ($data == '成功') {
                $this->logger(69);
                jsonResult($data);
            } else {
                $this->logger(70);
                jsonError($data);
            }
        }

    }


    //路由配置
    public function routeAction()
    {
        $param = input('get.') ? input('get.') : input('post.');
        if (!\Tools::isEmpty($param['type'])) {
            $type = decryptcode($param['type']);
            $param['destination'] = decryptcode(input('post.destination'));
            $param['gateway'] = decryptcode(input('post.gateway'));
            $param['genmask'] = decryptcode(input('post.genmask'));
            $param['eth'] = decryptcode(input('post.eth'));
            $data = $this->Configsystem->getRoute($type, $param);
            if (is_array($data)) {
                $res = encryptcode(json_encode($data, 1));
                jsonResult($res);
            } elseif ($data == '成功') {
                $res['status'] = $data;
                $this->logger(71);
                jsonResult($res);
            } else {
                $this->logger(72);
                jsonError($data);
            }
        }

    }


    //系统信息
    public function serviceAction()
    {
        $sensor_ip = $this->Configsystem->getServiceIp();
        $sensor_id = $this->Configsystem->getServiceId();
        $sensor_place = $this->Configsystem->getServicePlace();
        $interface_port = $this->Configsystem->getInterfacePort();
        $version = $this->Configsystem->getVersion();
        $time_out = $this->Configsystem->getTimeOut();
        $fail_count = $this->Configsystem->getFailCount();
        $fail_time = $this->Configsystem->getFailTime();

        $data['sensor_ip'] = $sensor_ip['sensor_info'];
        $data['sensor_id'] = $sensor_id;
        $data['place'] = $sensor_place;
        $data['version'] = $version;
        $data['interface_port'] = $interface_port['value'];
        $data['time_out'] = $time_out['time_out'];
        $data['fail_count'] = $fail_count['fail_count'];
        $data['fail_time'] = $fail_time['fail_time'];
        $res = encryptcode(json_encode($data, 1));
        jsonResult($res);
    }


    //设置系统信息
    public function setServiceAction()
    {
        $param = $_POST;

        if (!\Tools::isEmpty($param)) {

            $res_one = $this->Configsystem->setServiceIp(decryptcode($param['sensor_ip']));
            if ($res_one != '成功') {
                $this->logger(56);
                jsonError('请输入正确的IP地址');
            }

            $res_two = $this->Configsystem->setServicePlace(decryptcode($param['place']));
            if (is_string($res_two) && strlen($res_two) > 6) {
                $this->logger(56);
                jsonError('厂站信息不能为空');
            }

            $res_three = $this->Configsystem->setIneterfacePort(decryptcode($param['interface_port']));

            $time_out['time_out'] = decryptcode($param['time_out']);
            $res_four = $this->Configsystem->setTimeConfig($time_out);

            $fail_count['fail_count'] = decryptcode($param['fail_count']);
            $res_five = $this->Configsystem->setTimeConfig($fail_count);

            $fail_time['fail_time'] = decryptcode($param['fail_time']);
            $res_siex = $this->Configsystem->setTimeConfig($fail_time);

            if ($res_one == '成功' && ($res_two == '成功' || $res_two != '成功') && $res_three == '成功' && $res_four === true && $res_five === true && $res_siex === true) {
                $this->logger(55);
                jsonResult();
            }

        }
    }


    //系统刷新
    public function refreshAction()
    {
        $data = $this->Configsystem->refresh();
        jsonResult(['res' => $data]);
    }


    //事件转发
    public function syslogAction()
    {

        $param = input('get.') ? input('get.') : input('post.');
        if (!\Tools::isEmpty($param)) {
            $data['type'] = decryptcode($param['type']);
            $data['state'] = decryptcode($param['state']);
            $data['host'] = decryptcode($param['host']);
            $data['port'] = decryptcode($param['port']);
            $data = $this->Configsystem->Syslog($data);
            if ($data == '成功') {
                $this->logger(65);
            } elseif (is_array($data)) {

            } else {
                $this->logger(66);
            }

            $res = encryptcode(json_encode($data, 1));
            jsonResult($res);
        }

    }


    //告警转发
    public function alarmSyslogAction()
    {
        $param = input('get.') ? input('get.') : input('post.');
        if (!\Tools::isEmpty($param)) {
            $data['type'] = decryptcode($param['type']);
            $data['state'] = decryptcode($param['state']);
            $data['host'] = decryptcode($param['host']);
            $data['port'] = decryptcode($param['port']);
            $data = $this->Configsystem->AlarmSyslog($data);
            if (is_array($data)) {
                $res = encryptcode(json_encode($data, 1));
                jsonResult($res);
            } elseif ($data == '成功') {
                $this->logger(67);
                jsonResult($data);
            } else {
                $this->logger(68);
                jsonError($data);
            }

        }

    }


    //系统对时
    public function systemTimeAction()
    {
        if (input('post.')) {
            $data['serverTime'] = decryptcode(input('post.serverTime'));
            $data['ch'] = decryptcode(input('post.ch'));
            $data['server'] = decryptcode(input('post.server'));
            $data['time'] = decryptcode(input('post.time'));
            if (!\Tools::isEmpty($data)) {

                $data = $this->Configsystem->setSystemTime($data);
                if ($data == '成功') {
                    $this->logger(57);
                    jsonResult($data);
                } else {
                    $this->logger(58);
                    jsonError($data);
                }

            }
        }

        //get获取
        $data = $this->Configsystem->getSystemTime();
        $res = encryptcode(json_encode($data, 1));
        jsonResult($res);
    }


    //系统测试
    public function pingAction()
    {
        $param = input('post.');
        if (!\Tools::isEmpty($param['type'])) {
            $data = $this->Configsystem->ping($param);
            if (empty($data)) {
                $res['status'] = '网络不可达';
            } else {
                $res['status'] = $data;
            }

            jsonResult($res);
        }
    }


    //安全认证网关
    public function safeCertificateAction()
    {
        $ip = decryptcode(input('post.ip'));
        if (!\Tools::isEmpty($ip)) {
            $data['res'] = $this->Configsystem->safeCertificate($ip);
            if ($data['res'] == '成功') {
                $this->logger(59);
            } else {
                $this->logger(60);
            }

            jsonResult($data);
        }

        $data = $this->Configsystem->safeCertificate();
        $res = encryptcode(json_encode($data, 1));
        jsonResult($res);
    }


    //系统升级
    public function upgradeAction()
    {
        set_time_limit(300);
        $upload = new spUploadFile("/work/web/html/bdapi/upgrade", 'upgrade');
        //var_dump($_FILES);die;
        if (!\Tools::isEmpty($_FILES['file'])) {

            $url = $upload->upload_file($_FILES['file'], "zip");
            //  var_dump($url);die;
//            $url = getcwd().ltrim($url,'.');
            if ($upload->errmsg == '') {
                $data['res'] = $this->Configsystem->upgrade($url);
                unlink("/work/web/html/bdapi/upgrade/upgrade.zip");
                $this->logger(53);
                jsonResult($data);
            } else {
                $this->logger(54);
                jsonError($upload->errmsg);
            }
        } else {

            jsonError('无上传文件');
        }

    }


    //系统关机
    public function shutdownAction()
    {
        if (!\Tools::isEmpty(input('post.type'))) {
            $data = $this->Configsystem->shutdown(input('post.'));
            $this->logger(51);
            jsonResult($data);
        }
    }


    //系统重启
    public function restartAction()
    {
        if (!\Tools::isEmpty(input('post.type'))) {
            $data = $this->Configsystem->restart(input('post.'));
            $this->logger(52);
            jsonResult($data);
        }
    }

    //修改管理口IP
    public function editmanagementipAction()
    {
        if ($this->getRequest()->isPost()) {
            $data['eth'] = input('post.eth');
            $data['ip'] = input('post.ip');
            $data['mask'] = input('post.mask');
            $data['gateway'] = input('post.gateway');

            if (Tools::isEmpty($data['eth'])) {
                jsonError('无效的参数:eth');
            }
            if (Tools::isEmpty($data['mask'])) {
                jsonError('无效的参数:mask');
            }
            if (Tools::isEmpty($data['ip']) OR !filter_var($data['ip'], FILTER_VALIDATE_IP)) {
                jsonError('无效的参数:ip');
            }
            if (Tools::isEmpty($data['gateway']) OR !filter_var($data['gateway'], FILTER_VALIDATE_IP)) {
                jsonError('无效的参数:gateway');
            }

            $str = $this->Configsystem->editManagementIp($data);

            jsonResult(['msg' => $str], '操作成功');
        }

        if ($this->getRequest()->isGet()) {
            $result = $this->Configsystem->getManagementIp();
            jsonResult($result, '操作成功');
        }

    }

    //流量设置配置
    public function trafficAction()
    {
        $type = input('get.ty') ? input('get.ty') : input('post.ty');
        $data['data']['type'] = decryptcode(input('post.type'));
        $data['data']['port'] = decryptcode(input('post.port'));
        $data['data']['device'] = decryptcode(input('post.device'));
        $data['data']['threat_host'] = decryptcode(input('post.threat_host'));
        $data['data']['threat_host_port'] = decryptcode(input('post.threat_host_port'));
        $data['data']['es_threshold'] = decryptcode(input('post.es_threshold'));
        $data['data']['es_auto_backup'] = decryptcode(input('post.es_auto_backup'));
        if (!\Tools::isEmpty($type)) {
            $type = decryptcode($type);

            $data = $this->Configsystem->traffic($type, $data);
            if (is_array($data)) {
                $res = encryptcode(json_encode($data, 1));
                jsonResult($res);
            } elseif (is_string($data)) {
                $data = json_decode($data, 255);
                if ($data['error_msg'] == '成功') {
                    $this->logger(79);
                } else {
                    $this->logger(80);
                }
                jsonResult($data['error_msg']);
            }

        }

    }

    public function strategyAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            //post传的
            $info['strategy_num'] = decryptcode(input('post.strategy_num'));
            $info['strategy_time'] = decryptcode(input('post.strategy_time'));
            $data['res'] = $this->Configsystem->setStrategy($info);
            if ($data['res'] === true) {
                $this->logger(73);
            } else {
                $this->logger(74);
            }
            jsonResult($data);
        } else {
            $data = $this->Configsystem->getStrategy();
            $res = encryptcode(json_encode($data, 1));
            jsonResult($res);
        }
    }

    public function getStrategyIpsAction()
    {
        $page_size = input('post.page_size/d', 1);
        $page = input('post.page/d', 1);

        $key = $_SESSION['uid'] . '-'. __METHOD__.'-'.$page;
        if ($result = $this->Configsystem->redis->get($key)) {
            $result = json_decode($result,true);

        } else {

            $data = $this->Configsystem->getStrategyIps();

            if (!empty($data)) {
                $list = $this->split($data, $page_size);
                if ($page_size >= count($data)) {
                    $result['list'] = $data;
                } else {
                    $result['list'] = $list[$page - 1];
                }

                $result['total_num'] = count($data);
                $result['total_page'] = ceil($result['total_num'] / $page_size);

            } else {
                $result['list'] = [];
                $result['total_num'] = 0;
                $result['total_page'] = 0;
            }


            $this->Configsystem->setCache($key,$result,600);

        }


        jsonResult($result);
    }

    public function setStrategyIPAction()
    { 
        $data = json_decode(decryptcode($_POST['list']),1);
        $data = json_decode(decryptcode($_POST['list']),1);

        if (!\Tools::isEmpty($data)) {
            $ips = $this->Configsystem->getStrategyIps();
            $proto = ['TCP', 'UDP'];
            $info = [];

            foreach ($data as $k => &$v) {
                $where['ip'] = $v['sensor'] ? $v['sensor'] : 0;
                $where['port'] = 8801;
                $where['id'] = 0;
                $where['proto'] = $v['proto'] ? $v['proto'] : '';
                $where['address'] = $v['address'] ? $v['address'] : '';
                $where['number'] = $v['number'] ? $v['number'] : 0;

                if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
                    jsonError('无效的参数:ip');
                }
                if (Tools::isEmpty($where['address']) OR !filter_var($where['address'], FILTER_VALIDATE_IP)) {
                    jsonError('无效的参数:address');
                }
                if (!in_array($where['proto'], $proto)) {
                    jsonError('无效的参数:proto');
                }
                if ($where['number'] < 1 or $where['number'] > 65535) {
                    jsonError('无效的参数:number');
                }

                $ret = $this->model->saveWhite($where);
                $where = [];
                if (!$ret) {
                    array_push($info, $v['address']);
                }

                foreach ($ips as $kk => $vv) {
                    if ($v['address'] == $vv['address']) {
                        unset($ips[$kk]);
                    }
                }
                array_values($ips);
            }

            $arr = [];
            foreach ($ips as $key => $val) {
                array_push($arr, $val['address']);
            }

            $params['strategy_ips'] = implode("-", $arr);
            $this->Configsystem->updateStrategyIP($params);

            if (empty($info)) {
                $this->logger(103);
                jsonResult([], '添加推荐策略成功');
            } else {
                $str = implode("|", $info);
                $this->logger(104);
                jsonError('添加推荐策略部分成功（' . $str . ' 已存在)');
            }

        } else {
            jsonError('无效的参数');
        }
    }

    //数组按指定10个为一组的分割数组
    private function split($data, $num = 5)
    {

        $arrRet = array();
        if (\Tools::isEmpty($data)) {
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

}

?>