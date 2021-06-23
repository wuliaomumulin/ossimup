<?php

//数据导入
namespace App\Models;

use App\Models\HostTypes;

class Import extends Model
{
    public $error = [];//有问题的数据
    public $allRows = 0;//整理总行数·
    protected $data = array();//数据项
    private $mode = 'check';//模式：check|save
    protected $filter = array(' ','\(','\)','（','）','\[','\]','【','】','！','\!',',','，','=','\.','。','‘',"'",'"','“');//过滤字符

    /**
     * 文件解析或导入
     * @param $file String excel文件路径
     * @param $mode check|save
     */
    public function index($file = '', $mode = 'check')
    {

        try {

            $inputFileType = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($file);

            $sheetsnum = $objPHPExcel->getSheetCount();
            $host_type = new HostTypes;


            for ($i = 0; $i < $sheetsnum; $i++) {

                $sheet = $objPHPExcel->getSheet($i);

                $sheetTitle = $sheet->getTitle();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                //获取一行的数据
                for ($row = 1; $row <= $highestRow; $row++) {

                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                    $this->mode = $mode;

                    //判断第一行是否是title，并且名称符合
                    if ($row == 1) {

                        if (!empty(self::verifyTitle($rowData))) {
                            self::addReasons($rowData, "sheet{$sheetTitle}标题不符合规范");
                            break;
                        }
                    } else {

                        //验证数据第二项是否为空,为空就跳过
                        if (empty($rowData[1])) {
                            continue;
                        }
                        // 记录总行数
                        $this->allRows++;
                        $data = self::verifyItems($rowData,$host_type);
                        if ($data === false) {
                            continue;
                        }
                        self::insertDB($data);

                    }

                }
            }

        } catch (\Exception $e) {
            jsonError($e->getMessage());
        }

    }

    /**
     * 验证标题是否变化
     */
    private static function verifyTitle($compare)
    {
        //定义title
        $title = [
            '资产名称', 'IP', 'MAC', '资产价值', '告警数量', '资产种类', '系统', '经度', '纬度'
        ];
        $result = array_udiff($title, array_filter($compare), function ($a, $b) {
            if ($a === $b) {
                return 0;
            }

            return ($a > $b) ? 1 : -1;
        });

        return $result;
    }


    /**
     *  验证数据项
     *
     */
    private function verifyItems($data,$host_type)
    {
        if (filter_var($data[1], FILTER_VALIDATE_IP)) {
            $res['ip'] = $data[1];
        } else {
            self::addReasons($data, "IP格式不正确");
            return false;
        }

        if (mb_strlen($data[2]) == 12) {
            if (preg_match("/^[A-F0-9]{12}$/", $data[2])) {
                $res['mac'] = $data[2];
            } else {
                self::addReasons($data, "MAC地址不正确");
                return false;
            }
        } elseif (mb_strlen($data[2]) == 17) {
            if (preg_match("/^[A-F0-9]{2}(-[A-F0-9]{2}){5}$/", $data[2])) {
                $res['mac'] = substr($data[2], 0, 2) . substr($data[2], 3, 2) . substr($data[2], 6, 2) . substr($data[2], 9, 2) . substr($data[2], 12, 2) . substr($data[2], 15, 2);
            } else {
                self::addReasons($data, "MAC地址不正确");
                return false;
            }
        } elseif (mb_strlen($data[2]) != 12 && mb_strlen($data[2]) != 17) {
            self::addReasons($data, "MAC地址不正确");
            return false;
        }

        $data[3] = htmlspecialchars($data[3],ENT_QUOTES);
        $data[4] = htmlspecialchars($data[4],ENT_QUOTES);
        $data[5] = htmlspecialchars($data[5],ENT_QUOTES);
        $data[6] = htmlspecialchars($data[6],ENT_QUOTES);

        if (is_numeric($data[7]) && checkLon($data[7]) == true) {
            $res['lon'] = $data[7];
        } else {
            self::addReasons($data, "非法经度数值");
            return false;
        }

        if (is_numeric($data[8]) && checkLat($data[8]) == true) {
            $res['lat'] = $data[8];
        } else {
            self::addReasons($data, "非法纬度数值");
            return false;
        }

        $config = \Yaf\Registry::get("config");
        $res['id'] = uuid();
        $res['ctx'] = $config->application->ctx;
        $res['hostname'] = $data[0];
        $res['fqdns'] = '';
        $res['alert'] = 0;
        $res['persistence'] = 0;
        $res['nat'] = 0;
        $res['rrd_profile'] = 0;
        $res['lat'] = $res['lat'] == ''?'0':$res['lat'];
        $res['lon'] = $res['lon'] == ''?'0':$res['lon'];
        $res['icon'] = '';
        $res['country'] = '';
        $res['external_host'] = 0;
        $res['interface'] = '';
        $res['external_host'] = 0;
        $res['rrd_profile'] = 0;
        $res['value'] = $data[6];
        $res['property_ref'] = 3;
        $res['source_id'] = 1;
        $res['extra'] = '';
        $res['tzone'] = 0;
        if (!empty($data[5])) {
            if (strpos($data['5'], ':') === false) {
                $type = $host_type->getHostType($data[5]);
                $res['type']['type'] = 0;
                $res['type']['subtype'] = $type;
            }else{
                $arr = explode(':',$data[5]);
                $type = $host_type->getHostType($arr[0]);
                $types = $host_type->getHostType($arr[1]);
                $res['type']['type'] = $type;
                $res['type']['subtype'] = $types;
            }
        }else{
            $res['type']['type'] = 0;
            $res['type']['subtype'] = 0;
        }

        if($data[3] == '低'){
            $res['asset'] = 1;
        }elseif($data[3] == '中'){
            $res['asset'] = 2;
        }elseif($data[3] == '高'){
            $res['asset'] = 3;
        }else{
            $res['asset'] = 3;
        }
        return $res;
    }

    //入库
    private function insertDB($data)
    {
        //判断是添加还是更新
        $subsql1 = "select * from host_ip where ip = inet6_aton('{$data['ip']}')";
        $status = $this->query($subsql1);
//        if(!\Tools::isEmpty($status)){
//            //更新
//            $sql = "update host_ip set `ip` = INET6_ATON('{$data['ip']}'),`mac` = unhex('{$data['mac']}'),`interface` = '{$data['interface']}' where `host_id` = unhex('{$data['id']}')";
//            array_push($sqls,$sql);
//        }else{
//            //添加
//            $sql = "replace into host_ip(`host_id`,`ip`,`mac`,`interface`) values(unhex('{$data['id']}'),INET6_ATON('{$data['ip']}'),unhex('{$data['mac']}'),'{$data['interface']}')";
//            array_push($sqls,$sql);
//        }

        //表中IP已存在的不录入数据库
        if (\Tools::isEmpty($status)) {
            $sqls = [
                "insert into host(`id`,`ctx`,`hostname`,`external_host`,`fqdns`,`asset`,`rrd_profile`,`alert`,`persistence`,`nat`,`descr`,`lat`,`lon`,`country`,`icon`) values(unhex('{$data['id']}'),unhex('{$data['ctx']}'),'{$data['hostname']}',{$data['external_host']},'{$data['fqdns']}',{$data['asset']},'{$data['rrd_profile']}',{$data['alert']},{$data['persistence']},'{$data['nat']}','{$data['descr']}','{$data['lat']}','{$data['lon']}','{$data['country']}','{$data['icon']}')",
                "insert into host_properties(`host_id`,`property_ref`,`source_id`,`value`,`tzone`) values (unhex('{$data['id']}'),{$data['property_ref']},{$data['source_id']},'{$data['value']}',{$data['tzone']})",
                "insert into host_types(`host_id`,`type`,`subtype`) values(unhex('{$data['id']}'),{$data['type']['type']},{$data['type']['subtype']})"
            ];
            $sql = "insert into  host_ip(`host_id`,`ip`,`mac`,`interface`) values(unhex('{$data['id']}'),INET6_ATON('{$data['ip']}'),unhex('{$data['mac']}'),'{$data['interface']}')";


            array_push($sqls, $sql);

            $this->startTrans();

            foreach ($sqls as $sql) {
                $res = $this->execute($sql);

                if (!is_numeric($res)) {
                    self::addReasons($data, "数据不符合规范");
                } else {
                    $pool[] = $res;
                }

            }
            if (in_array(false, $pool, TRUE)) {
                $this->rollback();

            } else {
                $this->commit();
            }
        }

    }


    /**
     * 格式化数据
     */
    private function verifyFormatData($data)
    {
        return [
            'comname' => sensitive($this->filter, $data[0]),
            'facname' => sensitive($this->filter, $data[1]),
            'asset_90' => $data[2],//一区采集器
            'asset_91' => $data[3],//二区采集器
            'asset_92' => $data[4],//三区采集器
            'asset_88' => $data[5],//主平台
            'asset_89' => $data[6],//备平台
            'contact' => trim($data[7]),//联系人
            'status' => trim($data[8]),//状态
            'isp' => str_replace(' ', '', $data[9]),//云营商
            'memo' => $data[10],//备注
            'accept_time' => self::excelTime(trim($data[11])),//验收时间
            'channelnum' => trim($data[12]),//段号
            'factype' => trim($data[13]),//场站类型
            'is_upgrade' => $data[14],//是否升级
            'equnum' => trim($data[15]),//设备号
            'simnum' => trim($data[16]),//卡号
            'detail' => $data[17],//现场详情
        ];
    }

    /**
     * 格式化公司数据
     */
    private function formatCompany()
    {
        return [
            'comname' => $this->data['comname'],
        ];
    }

    /**
     * 格式化厂站数据
     */
    private function formatFactory()
    {
        return [
            'comid' => $this->comid,
            'facname' => $this->data['facname'],
            'factype' => self::formatFactype($this->data['factype']),
            'contact' => $this->data['contact'],
            'memo' => $this->data['memo'],
            'nickname' => \Tools::substr($this->data['facname'], 0, 7),
            'uid' => $this->getuid(),
            'channelnum' => $this->data['channelnum'],
            'status' => $this->data['status'],//状态
            'equnum' => $this->data['equnum'],
            'simnum' => $this->data['simnum'],
            'apply_time' => $this->data['accept_time'],
            'accept_time' => $this->data['accept_time'],
            'isp' => self::formatIsp($this->data['isp']),
        ];
    }

    /**
     * 厂站类型对应关系
     */
    private static function formatFactype($type)
    {

        $arr = [
            '风光' => '风光电',
            '水光' => '水光电',
            '光' => '光电',
            '风' => '风电',
            '火' => '火电',
            '水' => '水电',
            '核' => '核电',
            '燃机' => '燃机电',
            '风光电' => '风光电',
            '水光电' => '水光电',
            '光电' => '光电',
            '风电' => '风电',
            '火电' => '火电',
            '火电' => '水电',
            '核电' => '核电',
            '燃机电' => '燃机电',
            'unkown' => '未知',
        ];

        if (isset($arr[$type])) {
            return $arr[$type];
        } else {
            return $arr['unkown'];
        }
    }

    /**
     * 厂站运营商对应关系
     */
    private static function formatIsp($isp)
    {

        $arr = [
            '移动' => '移动',
            '联通' => '联通',
            '电信' => '电信',
            '铁通' => '铁通',
            '中国移动' => '移动',
            '中国联通' => '联通',
            '中国电信' => '电信',
            '中国铁通' => '铁通',
            'unkown' => '未知',
        ];

        if (isset($arr[$isp])) {
            return $arr[$isp];
        } else {
            return $arr['unkown'];
        }
    }

    /**
     * 格式化资产数据
     */
    private function formatAsset($data)
    {
        return [
            'asset_name' => str_replace(' ', '', $data['asset_name']),
            'asset_nickname' => str_replace(' ', '', $data['asset_nickname']),
            'asset_ip' => $data['asset_ip'],
            'asset_type' => $data['asset_type'],
            'device' => $data['device'],
            'es_id' => $data['es_id'],
            'sensor_id' => 'NOTFOUND',//sensor_id不存在
            'factory_id' => $data['factory_id'],
            'company_id' => $this->comid,
            'asset_status' => 1,//资产状态( 0:离线 1:在线)
            'asset_source' => 1,//资产来源（0：自动采集 1：手工录入）
            'asset_attrs' => '{"online":2,"status":1,"log":1,"link":1,"traffic":1}',
            'delete_status' => 0,
        ];
    }

    /**
     * IP原子性检测和组装
     */
    private function atomic()
    {
        //原子性检测
        $data = $this->data;
        if ($ip = self::checkIp($data['asset_88'])) {
            $data['asset_88'] = $ip;
        }
        if ($ip = self::checkIp($data['asset_89'])) {
            $data['asset_89'] = $ip;
        }
        if ($ip = self::checkIp($data['asset_90'])) {
            $data['asset_90'] = $ip;
        }
        if ($ip = self::checkIp($data['asset_91'])) {
            $data['asset_91'] = $ip;
        }
        if ($ip = self::checkIp($data['asset_92'])) {
            $data['asset_92'] = $ip;
        }
        //构建asset包

        $result = self::atomicIp($data);
        return $result;
    }

    /**
     * 检测公司是否存在
     */
    private function checkCompany()
    {
        $this->comid = $this->table('company')->where(['comname' => $this->data['comname']])->getField('comid');
        if (\Tools::isEmpty($this->comid)) {
            if (!\Tools::isEmpty($this->data['comname'])) self::addReasons($this->data, '公司不存在');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 检测厂站是否符合要求
     */
    private function checkFactory()
    {
        $facname = $this->data['facname'];
        $where = [
            'facname' => ['like', "%{$facname}%"],
        ];
        $factory = $this->table('factory')->where($where)->find();
        //如不为空，说明厂站已存在，不可以新增
        if (!\Tools::isEmpty($factory)) {
            $this->facid = $factory['facid'];
            //附加原因
            if (!\Tools::isEmpty($this->data['facname'])) self::addReasons($this->data, '厂站已存在');
            return FALSE;
        }

        //通道号
        if (!empty($this->data['channelnum'])) {
            $where = [
                'channelnum' => $this->data['channelnum'],
            ];
            $count = $this->table('factory')->where($where)->count();
            if ($count > 0) {
                if (!\Tools::isEmpty($this->data['facname'])) self::addReasons($this->data, '通道号已存在');
                return FALSE;
            }
        }


        //设备号
        if (!empty($this->data['equnum'])) {
            $where = [
                'equnum' => $this->data['equnum'],
            ];
            $count = $this->table('factory')->where($where)->count();
            if ($count > 0) {
                if (!\Tools::isEmpty($this->data['facname'])) self::addReasons($this->data, '设备号已存在');
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * 检测资产是否存在
     * @param String ip IP地址
     */
    private function checkAsset($ip)
    {
        if (self::ignore($ip)) {
            $ip = $this->table('asset')->where([
                'asset_ip' => $ip,
                'factory_id' => ['in', ['', 0]],
                'company_id' => ['in', ['', 0]],
            ])->find();

            if (!\Tools::isEmpty($ip)) {
                $this->mode = 'update';
                /*                if($this->verifyAssetAttrs($ip['asset_attrs'])){
                                    return TRUE;
                                }
                                if($ip['delete_status']==1){
                                    $this->mode = 'update';
                                    return TRUE;
                                }
                                if(!\Tools::isEmpty($this->data['facname'])) self::addReasons($this->data,'资产IP已存在');
                                return FALSE;*/
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 忽略空数据
     */
    private static function ignore($ip)
    {
        //为空忽略
        if (empty($ip)) {
            return FALSE;
        }
        //IP忽略
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 验证资产Attrs的status属性，如果为零,就将处理模式改为更新模式
     * return Bool TRUE|FALSE
     */
    private function verifyAssetAttrs($json)
    {
        $attrs = json_decode($json, true);
        if (!\Tools::isEmpty($attrs)) {
            //未管理状态
            if ($attrs['status'] == 0) {
                $this->mode = 'update';
                return TRUE;
            }
        }
        return FALSE;
    }

    /*
    /**
	* 添加厂站
    */
    private function addFactory()
    {

        $data = $this->formatFactory();

        if ($this->mode == 'save') {
            return $this->table('factory')->add($data);
        } else {
            return $this->table('factory')->where(['facname' => $data['facname']])->getField('facid');
        }
    }

    /**
     * 添加资产
     */
    private function addAsset()
    {
        $data = self::atomic();
        if ($this->mode == 'save') {
            $this->table('asset')->addAll($data);
        }
        if ($this->mode == 'update') {
            self::updateAsset($data);
        }
    }

    /**
     * 更新资产
     */
    private function updateAsset($assets)
    {
        if (self::getMode() == 'import') {
            foreach ($assets as $k => $a) {
                $asset = $this->table('asset')->where(['asset_ip' => $a['asset_ip']])->find();
                if (!\Tools::isEmpty($asset['asset_id'])) {

                    $attrs = json_decode($asset['asset_attrs'], TRUE);
                    $attrs['status'] = 1;
                    $assets[$k]['asset_attrs'] = json_encode($attrs);

                    $this->table('asset')->where(['asset_id' => $asset['asset_id']])->save($assets[$k]);
                } else {
                    $this->table('asset')->add($assets[$k]);
                }
            }
        }
    }

    /**
     * 附加原因
     * $data array 数据
     * $reasons string 原因
     */
    private function addReasons($data, $reasons)
    {
        $data = array_merge($data, ['reasons' => $reasons]);
        array_push($this->error, $data);
    }

    /**
     * IP合法性校验
     * $data array 数据
     * $reasons string 原因
     * ip分隔符可以是|或者,
     */
    private function checkIp($ip)
    {
        if (stripos($ip, '|')) {
            $ips = explode('|', $ip);
            $ips[0] = trim($ips[0]);
            if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\./', $ips[0], $matches)) {
                $num = sizeof($ips);
                for ($i = 1; $i < $num; $i++) {
                    if (filter_var($matches[0] . $ips[$i], FILTER_VALIDATE_IP)) {
                        $ips[$i] = $matches[0] . $ips[$i];
                    } else {
                        unset($ips[$i]);
                        if ($this->data['factory_id']) self::addReasons($this->data, 'ip不标准');
                    }
                }
                $ip = $ips;
            } else {
                self::addReasons($this->data, 'ip不标准');
            }
        } elseif (stripos($ip, ',')) {
            $ips = explode(',', $ip);
            $num = sizeof($ips);
            for ($i = 0; $i < $num; $i++) {
                $ips[$i] = trim($ips[$i]);
                if (!filter_var($ips[$i], FILTER_VALIDATE_IP)) {
                    unset($ips[$i]);
                    if ($this->data['factory_id']) self::addReasons($this->data, 'ip不标准');
                }
            }
            $ip = $ips;
        }
        if (is_string($ip) && !empty($ip)) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                if ($this->data['factory_id']) self::addReasons($this->data, 'ip不标准');
                return FALSE;
            }
        }
        return $ip;
    }

    /**
     * IP原子性校验
     * $data array 数据
     * $reasons string 原因
     * ip分隔符可以是|或者,
     */
    private function atomicIp($data)
    {
        //主平台
        if (is_array($data['asset_88'])) {
            foreach ($data['asset_88'] as $ip) {
                $input = [
                    'asset_name' => $data['facname'] . $data['factype'] . '主平台',
                    'asset_nickname' => $data['facname'] . $data['factype'] . '主平台',
                    'asset_ip' => $ip,
                    'asset_type' => 88,
                    'device' => $ip,
                    'es_id' => uuid(),
                    'factory_id' => $data['factory_id'],
                ];
                //验证Asset_ip是否通过验证
                if (self::checkAsset($ip)) {
                    $result[] = self::formatAsset($input);
                }
            }
        } else {
            $input = [
                'asset_name' => $data['facname'] . $data['factype'] . '主平台',
                'asset_nickname' => $data['facname'] . $data['factype'] . '主平台',
                'asset_ip' => $data['asset_88'],
                'asset_type' => 88,
                'device' => $data['asset_88'],
                'es_id' => uuid(),
                'factory_id' => $data['factory_id'],
            ];
            if (self::checkAsset($data['asset_88'])) {

                $result[] = self::formatAsset($input);
            }
        }

        //备平台
        if (is_array($data['asset_89'])) {
            foreach ($data['asset_89'] as $ip) {
                $input = [
                    'asset_name' => $data['facname'] . $data['factype'] . '备平台',
                    'asset_nickname' => $data['facname'] . $data['factype'] . '备平台',
                    'asset_ip' => $ip,
                    'asset_type' => 89,
                    'device' => $ip,
                    'es_id' => uuid(),
                    'factory_id' => $data['factory_id'],
                ];
                if (self::checkAsset($ip)) {
                    $result[] = self::formatAsset($input);
                }
            }
        } else {
            $input = [
                'asset_name' => $data['facname'] . $data['factype'] . '备平台',
                'asset_nickname' => $data['facname'] . $data['factype'] . '备平台',
                'asset_ip' => $data['asset_89'],
                'asset_type' => 89,
                'device' => $data['asset_89'],
                'es_id' => uuid(),
                'factory_id' => $data['factory_id'],
            ];
            if (self::checkAsset($data['asset_89'])) {
                $result[] = self::formatAsset($input);
            }
        }

        //I区采集器
        if (is_array($data['asset_90'])) {
            foreach ($data['asset_90'] as $ip) {
                $input = [
                    'asset_name' => $data['facname'] . $data['factype'] . 'I区采集器',
                    'asset_nickname' => $data['facname'] . $data['factype'] . 'I区采集器',
                    'asset_ip' => $ip,
                    'asset_type' => 90,
                    'device' => $ip,
                    'es_id' => uuid(),
                    'factory_id' => $data['factory_id'],
                ];
                if (self::checkAsset($ip)) {
                    $result[] = self::formatAsset($input);
                }
            }
        } else {
            $input = [
                'asset_name' => $data['facname'] . $data['factype'] . 'I区采集器',
                'asset_nickname' => $data['facname'] . $data['factype'] . 'I区采集器',
                'asset_ip' => $data['asset_90'],
                'asset_type' => 90,
                'device' => $data['asset_90'],
                'es_id' => uuid(),
                'factory_id' => $data['factory_id'],
            ];
            if (self::checkAsset($data['asset_90'])) {
                $result[] = self::formatAsset($input);
            }
        }

        //II区采集器
        if (is_array($data['asset_91'])) {
            foreach ($data['asset_91'] as $ip) {
                $input = [
                    'asset_name' => $data['facname'] . $data['factype'] . 'II区采集器',
                    'asset_nickname' => $data['facname'] . $data['factype'] . 'II区采集器',
                    'asset_ip' => $ip,
                    'asset_type' => 91,
                    'device' => $ip,
                    'es_id' => uuid(),
                    'factory_id' => $data['factory_id'],
                ];
                if (self::checkAsset($ip)) {
                    $result[] = self::formatAsset($input);
                }
            }
        } else {
            $input = [
                'asset_name' => $data['facname'] . $data['factype'] . 'II区采集器',
                'asset_nickname' => $data['facname'] . $data['factype'] . 'II区采集器',
                'asset_ip' => $data['asset_91'],
                'asset_type' => 91,
                'device' => $data['asset_91'],
                'es_id' => uuid(),
                'factory_id' => $data['factory_id'],
            ];
            if (self::checkAsset($data['asset_91'])) {
                $result[] = self::formatAsset($input);
            }
        }

        //III区采集器
        if (is_array($data['asset_92'])) {
            foreach ($data['asset_92'] as $ip) {
                $input = [
                    'asset_name' => $data['facname'] . $data['factype'] . 'III区采集器',
                    'asset_nickname' => $data['facname'] . $data['factype'] . 'III区采集器',
                    'asset_ip' => $ip,
                    'asset_type' => 92,
                    'device' => $ip,
                    'es_id' => uuid(),
                    'factory_id' => $data['factory_id'],
                ];
                if (self::checkAsset($ip)) {
                    $result[] = self::formatAsset($input);
                }
            }
        } else {
            $input = [
                'asset_name' => $data['facname'] . $data['factype'] . 'III区采集器',
                'asset_nickname' => $data['facname'] . $data['factype'] . 'III区采集器',
                'asset_ip' => $data['asset_92'],
                'asset_type' => 92,
                'device' => $data['asset_92'],
                'es_id' => uuid(),
                'factory_id' => $data['factory_id'],
            ];
            if (self::checkAsset($data['asset_92'])) {
                $result[] = self::formatAsset($input);
            }
        }

        return $result;
    }

    /**
     * 从URL获取导入模式
     */
    private static function getMode()
    {
        return array_pop(explode('/', $_SERVER['REQUEST_URI']));
    }

    /**
     * 时间处理方法
     */
    private function excelTime($date)
    {
        $type1 = strpos($date, '/');
        $type2 = strpos($date, '-');
        if ($type1 || $type2) {
            return $date;
        } else {
            return date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($date));
        }
    }

}