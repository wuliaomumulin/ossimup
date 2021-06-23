<?php

use App\Models\UdpSensor;
use App\Models\Host;
use App\Models\Userreference;
use App\Models\User;

/**
 * 采集器
 */
class SensorController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
    protected $num = 42;

    public function init()
    {
        parent::init();

        $this->config = \Yaf\Registry::get("config");
        $this->model = new UdpSensor();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        set_time_limit(20);
        $this->checkAuth($this->num);
    }

    public function querylistAction()
    {
        $datalist = $this->model->allAgent();
        foreach ($datalist as $k => $v) {
            $datalist[$k]['ip_port'] = $v['ip'] . '-' . $v['port'];
        }
        jsonResult($datalist);
    }


    //详情
    public function detailAction()
    {
        $where['ip'] = input('ip/s', '');//32位

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }
        $datalist = $this->model->getOne($where);
        jsonResult($datalist);
    }


    /**
     * 采集器调阅
     */
    public function collectAction()
    {

        $where['ip'] = input('get.ip/s', 0);//
        $where['port'] = input('get.port/d', 8801);//
        $where['para'] = input('get.para/d', 1);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }

        jsonResult($this->model->browse($where));
    }

    /**
     * 采集器调阅更新
     */
    public function updateAction()
    {
        $where['ip'] = input('get.ip/s', 0);//
        $where['port'] = input('get.port/d', 8801);//
        $where['para'] = input('get.para/d', 1);//
        $where['host_id'] = input('get.host_id/s', 1);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }

        jsonResult($this->model->browse_upd($where));
    }


    /**
     * 资产配置
     */
    public function configassetAction()
    {
        $params['act'] = input('post.act/s', 'add');
        $params['ip'] = input('post.ip/s', '');
        $params['id'] = input('post.id/s', '');
        $params['port'] = input('post.port/d', 8801);
        $params['assert_name'] = input('post.assert_name/s', '');
        $params['ipaddra'] = input('post.ipaddra/s', '');
        $params['maca'] = str_replace('-', '', input('post.maca/s', ''));
        $params['ipaddrb'] = input('post.ipaddrb/s', '');
        $params['macb'] = str_replace('-', '', input('post.macb/s', ''));
        $params['devtype'] = input('post.devtype/s', 0);
        $params['seller'] = input('post.seller/s', '');
        $params['csn'] = input('post.csn/s', '');
        $params['ostype'] = input('post.ostype/s', '');
        $params['snmptype'] = input('post.snmptype/s', 0);
        $params['username'] = input('post.username/s', '');
        $params['authtype'] = input('post.authtype/s', '');
        $params['encrypttype'] = input('post.encrypttype/s', '');
        $params['snmpread'] = input('post.snmpread/s', '');
        $params['snmpwrite'] = input('post.snmpwrite/s', '');
        /** 检测审计 **/
        $params['innername'] = input('post.innername/s', '');
        $params['hostname'] = input('post.hostname/s', '');
        $params['voltagelevel'] = input('post.voltagelevel/s', '');
        $params['area'] = input('post.area/s', '');
        $params['stationtype'] = input('post.stationtype/s', '');
        $params['physicallocation'] = input('post.physicallocation/s', '');
        $params['vendorguid'] = input('post.vendorguid/s', '');
        $params['model'] = input('post.model/s', '');
        $params['dcdip'] = input('post.dcdip/s', '');
        $params['softwareversion'] = input('post.softwareversion/s', '');
        $params['businesssystem'] = input('post.businesssystem/s', '');
        $params['devicepurpose'] = input('post.devicepurpose/s', '');
        $params['assetsource'] = input('post.assetsource/s', '');
        $params['matching'] = input('post.matching/d', 0);
        $params['person'] = input('post.person/s', '');
        $params['telphone'] = input('post.telphone/s', '');
        $params['securityarea'] = input('post.securityarea/s', '');

        if (\Tools::isEmpty($params['ip']) OR !filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }

        if (!\Tools::isEmpty($params['ipaddra'])) {
            if (!filter_var($params['ipaddra'], FILTER_VALIDATE_IP)) {
                jsonError('无效的参数:ipaddra');
            }
            if ($params['ipaddra'] == '127.0.0.1') {
                jsonError('无效的参数:ipaddra');
            }
        }


        if (!\Tools::isEmpty($params['ipaddrb'])) {
            if (!filter_var($params['ipaddrb'], FILTER_VALIDATE_IP)) {
                jsonError('无效的参数:ipaddrb');
            }
        }

        $ret = $this->model->configAsset($params);
        if ($ret) {
            $this->logger(13);
            jsonResult([], '操作成功');
        } else {
            $this->logger(14);
            //jsonError($this->model::$errors);
            jsonError('操作失败', $this->model::$errors);
        }

    }

    /***
     * 网关配置
     */
    public function confignetcardAction()
    {
        $params['act'] = input('post.act/s', 'add');
        $params['ip'] = input('post.ip/s', '');
        $params['port'] = input('post.port/d', 8801);
        $params['id'] = input('post.id/s', '');

        $params['netcard_name'] = input('post.netcard_name/s', '');
        $params['ipaddr'] = input('post.ipaddr/s', '');
        $params['submask'] = input('post.submask/s', '');
        $params['linkstatus'] = input('post.linkstatus/d', 0);
        $params['enablestatus'] = input('post.enablestatus/d', 0);
        $params['idsport'] = input('post.idsport/d', 0);

        if (\Tools::isEmpty($params['ip']) OR !filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:ip');
        }

        if (!\Tools::isEmpty($params['ipaddr'])) {
            if (!filter_var($params['ipaddr'], FILTER_VALIDATE_IP)) {
                jsonError('无效的参数:ipaddr');
            }
        }

        $ret = $this->model->configNetcard($params);
        if ($ret) {
            $this->logger(15);
            jsonResult([], '操作成功');
        } else {
            $this->logger(16);
            jsonError('网卡配置失败,参数有误', $this->model::$errors);
        }

    }

    /***
     * 路由配置
     */
    public function configrouterAction()
    {
        $params['act'] = input('post.act/s', 'add');
        $params['ip'] = input('post.ip/s', '');
        $params['port'] = input('post.port/d', 8801);
        $params['id'] = input('post.id/s', '');

        $params['ipaddr'] = input('post.ipaddr/s', '');
        $params['submask'] = input('post.submask/s', '');
        $params['gateway'] = input('post.gateway/s', '');

        if (\Tools::isEmpty($params['ip']) OR !filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:ip');
        }

        if (!\Tools::isEmpty($params['ipaddr'])) {
            if (!filter_var($params['ipaddr'], FILTER_VALIDATE_IP)) {
                jsonError('无效的参数:ipaddr');
            }
        }

        $ret = $this->model->configRouter($params);
        if ($ret) {
            $this->logger(17);
            jsonResult([], '操作成功');
        } else {
            $this->logger(18);
            jsonError('路由配置失败,参数有误', $this->model::$errors);
        }
    }

    /***
     * 系统信息
     */
    public function configsysinfoAction()
    {
        $params['ip'] = input('post.ip/s', '');
        $params['port'] = input('post.port/d', 8801);

        $params['softver'] = input('post.softver/s', '');
        $params['sysip'] = input('post.sysip/s', '');
        $params['devmac'] = input('post.devmac/s', '');
        $params['devname'] = input('post.devname/s', '');
        $params['eventdevname'] = input('post.eventdevname/s', '');
        $params['vendor'] = input('post.vendor/s', '');

        if (\Tools::isEmpty($params['ip']) OR !filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:ip');
        }
        if (\Tools::isEmpty($params['sysip']) OR !filter_var($params['sysip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:sysip');
        }

        $ret = $this->model->configSysInfo($params);
        if ($ret) {
            $this->logger(19);
            jsonResult([], '操作成功');
        } else {
            $this->logger(20);
            jsonError($this->model::$errors);
        }
    }

    /***
     * 基础配置
     */
    public function configsyslogAction()
    {
        $params['ip'] = input('post.ip/s', '');
        $params['port'] = input('post.port/d', 8801);

        $params['state'] = input('post.state/s', '');
        $params['devip'] = input('post.devip/s', '');
        $params['logport'] = input('post.logport/s', '');
        $params['ntpmode'] = input('post.ntpmode/s', '');
        $params['beepmode'] = input('post.beepmode/s', '');
        $params['connmode'] = input('post.connmode/s', '');
        $params['pcap_state'] = input('post.pcap_state/s', 'on');
        $params['pcap_ip'] = input('post.pcap_ip/s', '');
        $params['pcap_port'] = input('post.pcap_port/s', '');
        $params['tp_state'] = input('post.tp_state/s', '');
        $params['tp_eth'] = input('post.tp_eth/s', '');

        if (\Tools::isEmpty($params['ip']) OR !filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:ip');
        }
        if (\Tools::isEmpty($params['devip']) OR !filter_var($params['devip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:devip');
        }

        $ret = $this->model->configSysLog($params);
        if ($ret) {
            $this->logger(21);
            jsonResult([], '操作成功');
        } else {
            $this->logger(22);
            jsonError($this->model::$errors);
        }
    }

    /***
     * 升级
     */
    public function upgradeAction()
    {
        set_time_limit(300);
        $ips = input('post.ips/s', '');

        //分割,
        if (stripos($ips, ',')) {
            $ips = explode(',', $ips);
        }

        if (!\Tools::isEmpty($_FILES['file'])) {

            /**
             *  删除过期文件
             */

            $url = "/work/sensor_manager/agent/upgrade";
            @chmod($url, 0777);
            if (file_exists($url . '/upgrade.zip')) {
                @unlink($url . '/upgrade.zip');
            }


            $upload = new spUploadFile($url, 'upgrade');

            $url = $upload->upload_file($_FILES['file'], "zip");

            if (empty($upload->errmsg)) {

                $ret = $this->model->configUpgrade($ips);
                if ($ret) {
                    $this->logger(23);
                    jsonResult($ret, '操作成功');
                } else {
                    $this->logger(24);
                    jsonError($this->model::$errors);
                }
            } else {
                jsonError($upload->errmsg);
            }
        } else {
            jsonError('无上传文件');
        }
    }

    /***
     * 获取全部设备类型
     */
    public function alltypeAction()
    {
        $result = $this->model->allDeviceType();
        jsonResult($result);
    }

    /**
     * 升级状态
     */
    public function upgradeloggerAction()
    {
        $ret = $this->model->upgradeLogger();
        if ($ret) {
            jsonResult($ret);
        } else {
            jsonError('升级日志文件文件不存在');
        }
    }

    /**
     * 设备管理基本信息
     * 2020-05-27
     */
    public function deviceAction()
    {
        $where['ip'] = input('ip/s', 0);//
        $where['port'] = input('port/d', 8801);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }

        if ($this->getRequest()->isGet()) {
            jsonResult($this->model->device($where));

        }
        if ($this->getRequest()->isPost()) {
            $data['contact'] = input('contact/s', '');//联系人
            $ret = $this->model->saveDevice($where, $data);
            if ($ret) {
                jsonResult('操作成功');
            } else {
                jsonError('操作失败');
            }
        }
    }

    /**
     * 规则列表--白名单
     */
    public function WhiteListAction()
    {
        $where['ip'] = input('ip/s', 0);//
        $where['port'] = input('port/d', 8801);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }

        $ret = $this->model->WhiteList($where);
        jsonResult($ret);

    }

    /**
     * 规则列表--白名单-保存
     */
    public function saveWhiteAction()
    {
        $where['ip'] = input('post.ip/s', 0);//
        $where['port'] = input('post.port/d', 8801);//
        $where['id'] = input('post.id/d', 0);//
        $where['proto'] = input('post.proto/s', '');//
        $where['address'] = input('post.address/s', '');//
        $where['number'] = input('post.number/d', 0);//
        //默认支持协议
        $proto = ['tcp', 'udp'];

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
        if ($ret) {
            jsonResult($this->model::$errors);
        } else {
            jsonError($this->model::$errors);
        }

    }

    /**
     * 规则列表--白名单-删除
     */
    public function destroyWhiteAction()
    {
        $where['ip'] = input('ip/s', 0);//
        $where['port'] = input('port/d', 8801);//
        $where['id'] = input('post.id/d', 0);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }
        if ($where['id'] == 0) {
            jsonError('无效的参数:id');
        }
        $ret = $this->model->destroyWhite($where);
        if ($ret) {
            jsonResult($this->model::$errors);
        } else {
            jsonError($this->model::$errors);
        }

    }

    /**
     * @abstract 获取采集器分区
     */
    public function get_sensorAction()
    {
        $ret = $this->model->get_sensor();
        jsonResult($ret);
    }

    public function allmonitorAction()
    {
        $datalist = $this->model->allMonitor();
        jsonResult($datalist);
    }

    /*
        更新频率
    */
    public function updatefrepAction()
    {
        $redis = new \phpredis();
        $key = $_SESSION['uid'] . '-' . __METHOD__;


        if ($this->getRequest()->isPost()) {
            $freq = input('post.freq', 300000);
            $redis->set($key, $freq);
            jsonResult($freq);
        }
        if ($this->getRequest()->isGet()) {
            if ($result = $redis->get($key)) {
                jsonResult($result);
            } else {
                jsonResult(300000);
            }
        }

    }
}