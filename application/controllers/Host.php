<?php

use App\Models\Host;
use App\Models\Log;
use App\Models\HostTypes;
use App\Models\DeviceTypes;
use App\Models\AssetOsTpye;
use App\Models\Userreference;
use App\Models\User;
use App\Models\Import;
use App\Models\Es;
use App\Models\UdpSensor;
use App\Models\Zhujws;
//
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class HostController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
    protected $conf = './list_conf/assetsManager.json';
    protected $num = 14;

    public function init()
    {
        parent::init();

        $this->config = \Yaf\Registry::get("config");
        $this->model = new Host();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        $this->checkAuth($this->num);

    }

    public function updateConfigAction()
    {
        $config = json_decode(file_get_contents($this->conf), 1);

        foreach ($config as $key => &$val) {
            if ($val['title'] == '资产种类') {
                $val['valueField'] = self::type();
            }
        }
        file_put_contents($this->conf, json_encode($config, 256));
        jsonResult();
    }

    //资产列表
    public function querylistAction()
    {
        $is_export = input('get.is_export/d', 1);  // 1 不处理    2 输出Excel
        if ($is_export == 2) {
            set_time_limit(300);//导出时五分钟超时
            $datalist = $this->model->getAll();
//            echo "<pre>";
//           var_dump($datalist);die;
            self::exportexcelquerylist($datalist['list']);
            //jsonResult($datalist);
        } else {
            $page = input('page/d', 1);
            $pagesize = input('page_size/d', 10);
        }

        $where = self::prev();
        $datalist = $this->model->getList($where, $page, $pagesize);
        jsonResult($datalist);
    }


    /**
     * 上传文件
     */
    public function uploadAction()
    {
        set_time_limit(300);
        $upload = new spUploadFile();
        if (!Tools::isEmpty($_FILES['file'])) {
            $upload->upload_file($_FILES['file'], "xls|xlsx|csv", 'excel/');
            if ($upload->errmsg == '') {


                $Import = new Import();

                $Import->index($upload->uploaded, 'check');
                $this->logger(6);
                jsonResult(
                    [
                        'data' => $Import->error,
                        'file' => $upload->uploaded,
                        'total_num' => $Import->allRows,
                    ]
                    , "上传文件成功");

            } else {
                $this->logger(7);
                jsonError($upload->errmsg);
            }
        } else {
            jsonError('无上传文件');
        }
    }


    /**
     * 解析文件
     */
    public function importAction()
    {
        set_time_limit(300);
        $file = input('post.file', '');
        if ($file <> '' && file_exists($file)) {
            $Import = new Import();
            $Import->index($file, 'save');
            jsonResult(
                [
                    $Import->error
                ]
                , "导入文件成功");
        } else {
            jsonError('无文件路径或文件不存在');
        }
    }


    /**
     * @abstract 资产数据导出   [这里因为服务器用的是PHP7.2版本 需要修改兼容性的问题(已经解决)]
     * @author   王晓辉
     * @param    和查询列表一样 查询什么出什么
     * @return   .xlsx
     */

    private function exportexcelquerylist($list)
    {

        //设置超时时间  5 Minute
        set_time_limit(300);

        //初始化Excel
        $obj = new PHPExcel();
        //引入初始Sheet
        $obj->getActiveSheetIndex(0);

        //初始化原始数据
        $fileName = '用户资产数据表' . date('Y-m-d%20H:i:s');

        // 初始化表头
        $arr = ['1' => ['资产名称', 'IP', 'MAC', '资产价值', '告警数量', '资产类型', '资产系统', '所属分区', '经度', '纬度']];

        //数据整合
        foreach ($list as $k => $v) {
            switch ($v['alert']) {
                case '0':
                    $v['alert'] = '无';
                    break;
                default:
                    $v['alert'] = '有';
                    break;
            }
            switch ($v['asset']) {
                case '1':
                    $v['asset'] = '低';
                    break;
                case '2':
                    $v['asset'] = '中';
                    break;
                case '3':
                    $v['asset'] = '高';
                    break;
                default:
                    break;
            }
            if ($v['os'] == '') {
                $v['os'] = '未知';
            }
            if ($v['type'] == ':') {
                $v['type'] = '未知';
            }

            $v['mac'] = substr($v['mac'], 0, 2) . '-' . substr($v['mac'], 2, 2) . '-' . substr($v['mac'], 4, 2) . '-' . substr($v['mac'], 6, 2) . '-' . substr($v['mac'], 8, 2) . '-' . substr($v['mac'], 10, 2);


            $arr[$k + 2] = [
                $v['hostname'], $v['ip'], $v['mac'], $v['asset'], $v['alert'], $v['type'], $v['os'], $v['name'], $v['lon'], $v['lat']
            ];
        }

        //初始化行
        $list = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        //获取长度
        $j_length = count($arr[1]);
        $i_length = count($arr);
        //数据循环导入
        for ($i = 1; $i <= $i_length; $i++) {

            for ($j = 0; $j < $j_length; $j++) {

                $obj->getActiveSheet()->setCellValue($list[$j] . $i, $arr[$i][$j]);
            }
        }

        // 设置当前sheet的名称
        $obj->getActiveSheet()->setTitle('资产列表');

        //设置长度
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(120);
        $obj->getActiveSheet()->getColumnDimension('B')->setWidth(80);
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $obj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $obj->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $obj->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $obj->getActiveSheet()->getColumnDimension('H')->setWidth(40);
        $obj->getActiveSheet()->getColumnDimension('I')->setWidth(40);
        $obj->getActiveSheet()->getColumnDimension('J')->setWidth(40);
        //清楚ob 缓存 防止乱码
        ob_clean();
        //建立输出区
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx');
        header('Cache-Control: max-age=1');
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        ob_end_clean();
        //输出结果
        $objWriter->save('php://output');
        $this->logger(5);
        exit;
    }


    //资产详情sa
    public function detailAction()
    {
        $id = input('id/s', '');//32位

        if (Tools::isEmpty($id)) {
            jsonError('无效的参数:id');
        }

        $where = [
            'id' => ['exp', "=unhex('{$id}')"]
        ];
        $datalist = $this->model->getOne($where);
        jsonResult($datalist);
    }

    /***
     * 前置方法，取出查询的唯一字段
     */
    public function prev()
    {
        $request = input('post.');
        //xss过滤事件
        foreach($request as $k => $v) $request[$k] = $this->model->xss_filter($v);


        unset($request['page'], $request['page_size']);

        //时间范围
        if (!\Tools::isEmpty($request['begindate']) and !\Tools::isEmpty($request['enddate'])) {
            $where['a.created'] = array(array('egt', $request['begindate']), array('elt', $request['enddate']));
            unset($request['begindate'], $request['enddate']);
        }
        //判断IP
        if (isset($request['ip'])) {
            if (filter_var($request['ip'], FILTER_VALIDATE_IP)) {
                $where['b.ip'] = ['exp', "=inet6_aton('{$request["ip"]}')"];
                unset($request['ip']);
            }
        }

        //资产种类判断
        if (isset($request['type'])) {
            if (stripos($request['type'], ',')) {
                $type = explode(',', $request['type']);
            } else {
                $type = [$request['type'], 0];
            }

            if (!Tools::isEmpty($type)) {
                $host_ids = (new HostTypes())->getHostId(
                    ['type' => $type[0], 'subtype' => $type[1]]
                );

                if (!Tools::isEmpty($host_ids)) {

                    $host_id_str = '';
                    array_walk($host_ids, function ($v) use (&$host_id_str) {
                        $host_id_str .= 'unhex("' . $v . '"),';
                    });

                    $where['a.id'] = ['exp', 'in(' . rtrim($host_id_str, ',') . ')'];

                } else {
                    $res['config'] = $this->model->getConfig();
                    $res['list'] = [];
                    $res['total_num'] = 0;
                    jsonResult($res);

                }
            } else {
                $res['config'] = $this->model->getConfig();
                $res['list'] = [];
                $res['total_num'] = 0;
                jsonResult($res);
            }
            unset($request['type']);
        }


        //根据ID搜索
        if (isset($request['id'])) {
            $where['a.id'] = ['exp', "=unhex('{$request["id"]}')"];
            unset($request['id']);
        }
        //资产名称
        if (isset($request['hostname'])) {
            $where['a.hostname'] = ['like', "%{$request["hostname"]}%"];
            unset($request['hostname']);
        }
        //mac地址
        if (isset($request['mac'])) {
            $where['b.mac'] = ['exp', "=unhex('{$request["mac"]}')"];
            unset($request['mac']);
        }

        //判断所属分区
        if (isset($request['name']) && !empty($request['name'])) {
            $where['d.name'] = ['exp', "='{$request["name"]}'"];
            unset($request['name']);
        }

        //开放端口
        if (!\Tools::isEmpty($request['ports'])) {
            $where['locate("\"port\":' . $request['ports'] . '",f.service)'] = ['gt', 0];
            unset($request['ports']);
        }

        //默认等于匹配
        if (!Tools::isEmpty($request)) {
            if (\Tools::is_single_array($request)) {
                $where['a.' . key($request)] = current($request);
            } else {
                foreach ($request as $k => $v) {
                    $where['a.' . $k] = $v;
                }
            }
        }


        return $where;
    }


    /**
     * @abstract 获取资产类型
     */
    public function typeAction()
    {
        $DeviceTypes = new DeviceTypes();
        $datalist = $DeviceTypes->tree();

        if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
            jsonResult($datalist);
        } else {
            foreach ($datalist as $k => $v) {

                if ($v['name'] == '采集器装置') {

                    unset($datalist[$k]);
                }
            }

            jsonResult(array_values($datalist));
        }


    }

    private function type()
    {
        $DeviceTypes = new DeviceTypes();
        $datalist = $DeviceTypes->tree();

        if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
            return $datalist;
        } else {
            foreach ($datalist as $k => $v) {

                if ($v['name'] == '采集器装置') {

                    unset($datalist[$k]);
                }
            }

            return array_values($datalist);
        }

    }

    //获取操作系统
    public function osAction()
    {
        $keyword = input('keyword');
        $where = [];
        if (!empty($keyword)) {
            $where['a.asset_os_name'] = array('like', "%{$keyword}%");
        }
        $asset_os_type = new AssetOsTpye();
        $datalist = $asset_os_type->where($where)->select();
        jsonResult($datalist);
    }


    public function saveAllLiuAction()
    {
        //设置超时时间  5 Minute
        set_time_limit(300);
        $data = json_decode(file_get_contents("php://input"), 1);
        $rs = [];
        $ips = [];

        foreach ($data as $k => $v) {

            $rs[$k]["id"] = uuid();
            $rs[$k]["ctx"] = $this->config->application->ctx;
            $rs[$k]["asset"] = 1;   //默认资产价值  低  1
            $rs[$k]["alert"] = decryptcode($v['alert']);
            $rs[$k]["hostname"] = decryptcode($v["name"]);
            $rs[$k]["fqdns"] = '';
            $rs[$k]["persistence"] = 0;
            $rs[$k]["nat"] = 0;
            $rs[$k]["rrd_profile"] = 0;
            $rs[$k]["descr"] = decryptcode($v["description"]);
            $rs[$k]["lat"] = 0;
            $rs[$k]["lon"] = 0;
            $rs[$k]["icon"] = "";
            $rs[$k]["country"] = "";
            $rs[$k]["external_host"] = 0;
            $rs[$k]["ip"] = decryptcode($v["ip"]);
            //  $rs[$k]["mac"] = strtoupper(str_replace(':', '', $v['mac']));  00-00-00-00-00-00
            $mac = decryptcode($v['mac']);
            $rs[$k]["mac"] = substr($mac, 0, 2) . substr($mac, 3, 2) . substr($mac, 6, 2) . substr($mac, 9, 2) . substr($mac, 12, 2) . substr($mac, 15, 2);
            $rs[$k]["interface"] = "";
            $rs[$k]["type"] = json_decode(decryptcode($v["type"]))[0];
            $rs[$k]["subtype"] = json_decode(decryptcode($v["type"]))[1];
            $rs[$k]["os"] = decryptcode($v['os']);
            $rs[$k]["model"] = decryptcode($v['model']);
            $rs[$k]["hids_id"] = decryptcode($v['id']);

            $rs[$k]["sensor"] = $this->model->getsensorid(decryptcode($v['device']));

            //去重
            $res = $this->model->quchong($rs[$k]);

            if ($res !== true) {
                $res = $this->model->synchronousHostLiu($rs[$k]);
                if (!empty($res)) {
                    array_push($ips, $res);
                }
            }

        }
        if (!empty($ips)) {
            $this->logger(4);
            $str = implode(',', $ips);
            jsonError('IP为' . $str . '的导入失败');
        } else {
            $this->logger(3);
            jsonResult([], '导入成功');
        }

    }

    public function saveAllZjAction()
    {
        //设置超时时间  5 Minute
        set_time_limit(300);
        $data = json_decode(file_get_contents("php://input"), 1);

        $rs = [];
        $ips = [];

        foreach ($data as $k => $v) {

            $rs[$k]["id"] = uuid();
            $rs[$k]["ctx"] = $this->config->application->ctx;
            $rs[$k]["asset"] = 1;   //默认资产价值  低  1
            $rs[$k]["alert"] = decryptcode($v['alert']);
            $rs[$k]["hostname"] = decryptcode($v["name"]);
            $rs[$k]["fqdns"] = '';
            $rs[$k]["persistence"] = 0;
            $rs[$k]["nat"] = 0;
            $rs[$k]["rrd_profile"] = 0;
            $rs[$k]["descr"] = decryptcode($v["description"]);
            $rs[$k]["lat"] = 0;
            $rs[$k]["lon"] = 0;
            $rs[$k]["icon"] = "";
            $rs[$k]["country"] = "";
            $rs[$k]["external_host"] = 0;
            $rs[$k]["ip"] = decryptcode($v["ip"]);
            //  $rs[$k]["mac"] = strtoupper(str_replace(':', '', $v['mac']));  00-00-00-00-00-00
            $mac = decryptcode($v['mac']);
            $rs[$k]["mac"] = substr($mac, 0, 2) . substr($mac, 3, 2) . substr($mac, 6, 2) . substr($mac, 9, 2) . substr($mac, 12, 2) . substr($mac, 15, 2);
            $rs[$k]["interface"] = "";
            $rs[$k]["type"] = json_decode(decryptcode($v["type"]))[0];
            $rs[$k]["subtype"] = json_decode(decryptcode($v["type"]))[1];
            $rs[$k]["os"] = decryptcode($v['os']);
            $rs[$k]["model"] = decryptcode($v['model']);
            $rs[$k]["hids_id"] = decryptcode($v['id']);

            $rs[$k]["sensor"] = $this->model->getsensorid(decryptcode($v['device']));

            //去重
            $res = $this->model->quchong($rs[$k]);

            if ($res !== true) {
                $res = $this->model->synchronousHostZj($rs[$k]);
                if (!empty($res)) {
                    array_push($ips, $res);
                }
            }

        }
        if (!empty($ips)) {
            $this->logger(4);
            $str = implode(',', $ips);
            jsonError('IP为' . $str . '的导入失败');
        } else {
            $this->logger(3);
            jsonResult([], '导入成功');
        }

    }

    /**
     * @abstract
     */
    public function saveAction()
    {
        //接值
        $data['id'] = input('post.id')?decryptcode(input('post.id')):'';//32位
        $data['ctx'] = input('post.ctx')?decryptcode(input('post.ctx')):$this->config->application->ctx;
        $data['hostname'] = input('post.hostname')?decryptcode(input('post.hostname')):'';
        $data['asset'] = input('post.asset')?decryptcode(input('post.asset')):2;
        $data['fqdns'] = input('post.fqdns')?decryptcode(input('post.fqdns')):'';
        $data['alert'] = input('post.alert')?decryptcode(input('post.alert')):0;
        $data['persistence'] = input('post.persistence')?decryptcode(input('post.persistence')):0;
        $data['nat'] = input('post.nat')?decryptcode(input('post.nat')):0;
        $data['rrd_profile'] = input('post.rrd_profile')?decryptcode(input('post.rrd_profile')):0;
        $data['descr'] = input('post.descr')?decryptcode(input('post.descr')):'';
        $data['lat'] = input('post.lat')?decryptcode(input('post.lat')):0;
        $data['lon'] = input('post.lon')?decryptcode(input('post.lon')):0;
        $data['icon'] = input('post.icon')?decryptcode(input('post.icon')):0;
        $data['country'] = input('post.country')?decryptcode(input('post.country')):'';
        $data['external_host'] = input('post.external_host')?decryptcode(input('post.external_host')):0;
        $data['ip'] = input('post.ip')?decryptcode(input('post.ip')):'';
        $data['mac'] = input('post.mac')?decryptcode(input('post.mac')):'';
        $data['interface'] = input('post.interface')?decryptcode(input('post.interface')):'';
        $data['type'] = input('post.type')?json_decode(decryptcode(input('post.type'))):0;
        $data['subtype'] = input('post.subtype')?decryptcode(input('post.subtype')):0;
        $data['os'] = input('post.os')?decryptcode(input('post.os')):'';
        $data['host_id'] = input('post.host_id')?decryptcode(input('post.host_id')):'';

        //字符串小写转大写
        $data['mac'] = strtoupper(str_replace('-', '', $data['mac']));


        if (Tools::isEmpty($data['id'])) {
            $data['id'] = uuid();
        }
        if (Tools::isEmpty($data['lat'])) {
            $data['lat'] = 0;
        }
        if (Tools::isEmpty($data['lon'])) {
            $data['lon'] = 0;
        }
        if (Tools::isEmpty($data['external_host'])) {
            $data['external_host'] = 0;
        }
        if (Tools::isEmpty($data['hostname'])) {
            jsonError('资产名称不允许为空');
        }
        if (Tools::isEmpty($data['ip']) OR !filter_var($data['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }


        $result = $this->model->saveHost($data);

        if (intval($result) > 0) {

            $this->logger(3);

            jsonResult([], '保存资产成功');
        } else {
            $this->logger(4);
            jsonError('保存资产失败');
        }

    }

    /**
     * 删除资产
     */
    public function destroyDoAction()
    {
        $ids = input('post.');//32位

        //xss过滤事件
        $ids = $this->model->xss_filter($ids);

        $result = $this->model->delHost($ids);

        if (intval($result) > 0) {
            $this->logger(1);
            jsonResult([], '删除资产成功');
        } else {
            $this->logger(2);
            jsonError('删除资产失败');
        }
    }

    /**
     * 删除资产
     */
    private function destroyDo($ids)
    {

        $result = $this->model->delHost($ids);

        if (intval($result) > 0) {
            $this->logger(1);
            jsonResult([], '删除资产成功');
        } else {
            $this->logger(2);
            jsonError('删除资产失败');
        }
    }


    /*
     * 有没有采集器
     * */

    public function destroyAction()
    {
        $ids = input('post.id/s', 0);//32位

        if (empty($ids)) jsonError('ID参数缺失');
        //xss过滤事件
        $ids = $this->model->xss_filter($ids);


        $ids = explode(',', $ids);
        $res = $this->model->isSensor($ids);

        if (!empty($res['sensor'])) {
            jsonError('所选资产含有关联关系(如平台,采集器等),将会删除其下所有资产！', $res);
        }
        $this->destroyDo($res);
    }

    /**
     * @abstract 获取一级资产类型
     */
    public function type1Action()
    {
        $DeviceTypes = new DeviceTypes();
        $datalist = $DeviceTypes->type1();

        if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
            jsonResult($datalist);
        } else {
            foreach ($datalist as $k => $v) {
                if ($v['name'] == '采集器装置') {
                    unset($datalist[$k]);
                }
            }
            jsonResult($datalist);
        }

        jsonResult($datalist);

    }

    /**
     * 网络拓扑中获取资产数据  取当日数据
     * @return [type] [description]
     */

    public function getTopologyAssetAction()
    {
        $page = input('post.page/d', 1);
        $page_size = input('post.page_size/d', 50);
        $data = $this->model->getTopologyAsset($page, $page_size);
        $data['total_page'] = ceil($data['total_num'] / $page_size);
        jsonResult($data);
    }

    /**
     *   获取主机卫士资产
     */
    public function getZhujwsAssetAction()
    {
        $redis = new \phpredis();
        if ($redis->get('ZhujwsAsset')) {
            $datalist = json_decode($redis->get('ZhujwsAsset'), 1);
        } else {
            $post['gid'] = input('post.gid/d', 0);
            $post['tid'] = input('post.tid/s', null);
            $post['status'] = input('post.status/d', 1);
            $post['search'] = input('post.search/s', '');
            $post['host_id'] = input('post.host_id/s', '');
            $post['page'] = 1;
            $post['limit'] = 10000;
            $post['sort'] = input('post.sort/a', (object)[]);

            $model = new Zhujws();
            $data = $model->getHostList($post);

            $datalist = [];
            foreach ($data['hostlist'] as $k => $v) {
                $datalist[$k]['alert'] = 0;
                $datalist[$k]['asset'] = 0;
                $datalist[$k]['description'] = '';
                $datalist[$k]['device'] = $v['sensorip'];
                $datalist[$k]['device_name'] = $v['sensorname'];
                $datalist[$k]['id'] = $v['hid'];
                $datalist[$k]['ip'] = $v['ipaddr'];
                $datalist[$k]['mac'] = '';
                $datalist[$k]['model'] = '';
                $datalist[$k]['name'] = $v['hostname'];
                $datalist[$k]['os'] = ($v['os_type'] == 2) ? 'windows' : 'linux';
                $datalist[$k]['type'] = ["1", "122"];
            }

            $redis->set('ZhujwsAsset', json_encode($datalist, 256), 0, 0, 300);
        }

        $page_size = input('post.page_size', 1);
        $list = $this->split($datalist, $page_size);

        $page = input('post.page', 1);
        if ($page_size >= count($datalist)) {

            $result['list'] = $datalist;
        } else {
            $result['list'] = $list[$page - 1];


        }

        $result['total_num'] = count($datalist);
        $result['total_page'] = ceil($result['total_num'] / $page_size);

        jsonResult($result);

    }

    //数组按指定10个为一组的分割数组
    private function split($data, $num = 5)
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


    //修改展示的list
    public function updateListConfigAction()
    {
        $str = input('post.name');
        //xss过滤事件
        $str = $this->model->xss_filter($str);

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


//    public function getTopologyAssetAction(){
//
//
//        $gte = str_replace(" ", "T", date("Y-m-d H:i:s",time()-86400).".100Z") ;
//
//        $lte = str_replace(" ", "T", date("Y-m-d H:i:s",time()).".100Z") ;
//
//        //$gte = "2020-10-01"."T00:00:00.100Z";
//        //$lte = "2020-10-20"."T23:59:59.100Z";
//        $es = '{
//                  "aggs": {
//                    "2": {
//                      "terms": {
//                        "field": "Metadata.LLDP.SysName",
//                        "order": {
//                          "_count": "desc"
//                        },
//                        "size": 1000
//                      },
//                      "aggs": {
//                        "3": {
//                          "terms": {
//                            "field": "Metadata.LLDP.MgmtAddress",
//                            "order": {
//                              "_count": "desc"
//                            },
//                            "size": 1000
//                          },
//                          "aggs": {
//                            "4": {
//                              "terms": {
//                                "field": "Metadata.LLDP.Description",
//                                "order": {
//                                  "_count": "desc"
//                                },
//                                "size": 1000
//                              },
//                              "aggs": {
//                                "5": {
//                                  "terms": {
//                                    "field": "Metadata.Type",
//                                    "order": {
//                                      "_count": "desc"
//                                    },
//                                    "size": 1000
//                                  },
//                                  "aggs": {
//                                    "6": {
//                                      "terms": {
//                                        "field": "Metadata.LLDP.ChassisID",
//                                        "order": {
//                                          "_count": "desc"
//                                        },
//                                        "size": 1000
//                                      },
//                                      "aggs": {
//                                        "7": {
//                                          "terms": {
//                                            "field": "Host",
//                                            "order": {
//                                              "_count": "desc"
//                                            },
//                                            "size": 1000
//                                          }
//                                        }
//                                      }
//                                    }
//                                  }
//                                }
//                              }
//                            }
//                          }
//                        }
//                      }
//                    }
//                  },
//                  "size": 0,
//                  "stored_fields": [
//                    "*"
//                  ],
//
//                  "docvalue_fields": [
//                    {
//                      "field": "CreatedAt",
//                      "format": "date_time"
//                    },
//                    {
//                      "field": "UpdatedAt",
//                      "format": "date_time"
//                    }
//                  ],
//                  "query": {
//                    "bool": {
//                      "filter": [
//                        {
//                          "range": {
//                            "CreatedAt": {
//                              "gte": "'.$gte.'",
//                              "lte": "'.$lte.'",
//                              "format": "strict_date_optional_time"
//                            }
//                          }
//                        }
//                      ]
//                    }
//                  }
//            }';
//        $index = "skydive_topology_live*";
//
//        $info = (new Es())->query($index,$es)["aggregations"][2]["buckets"];
//
//
//        if(empty($info)){
//            jsonResult([]);
//        }
//        $sql = "SELECT inet6_ntoa(b.ip) ip FROM `host` a LEFT JOIN host_ip b ON a.id = b.host_id GROUP BY b.ip";
//
//        $ip = array_column($this->model->query($sql), "ip");
//        $sensor_list =  (new UdpSensor())->get_sensor();
//        $sensor = [];
//        foreach ($sensor_list as $k => $v) {
//            $sensor[$v["name"]] = $v["host_id"];
//        }
//
//
//        $rs = [];
//
//        foreach ($info as $k => $v) {
//            foreach ($v[3]['buckets'] as $ke => $va) {
//                //已经保存的数据不在重复获取  IP有问题的数据也不咋
//                if($va["key"] != '0.0.0.0' && !in_array($va["key"], $ip)){
//                    $host_id = $sensor[$va["4"]["buckets"]["0"]["5"]["buckets"]["0"]["6"]["buckets"]["0"]["7"]["buckets"]["0"]["key"]];
//
//                    if(empty($host_id) || $host_id == null) continue;
//                    $rs[$va["key"]]["hostname"] = $v["key"];
//                    $rs[$va["key"]]["ip"] = $va["key"];
//                    $rs[$va["key"]]["descr"] = $va["4"]["buckets"]["0"]["key"];
//                    $rs[$va["key"]]["mac"] = $va["4"]["buckets"]["0"]["5"]["buckets"]["0"]["6"]["buckets"]["0"]["key"];
//                    $rs[$va["key"]]["host_id"] = $host_id;
//
//                    if(strstr($rs[$va["key"]]["descr"],"Siemens")){
//                        $rs[$va["key"]]["type"] = ["6","412"];
//                    }else{
//                        $rs[$va["key"]]["type"] = ["9"];
//                    }
//               }
//            }
//        }
//
//        $rs = array_values($rs);
//
//        jsonResult($rs);
//    }

    public function macFlushAction()
    {
        $this->model->macFlush();
        jsonResult([], '资产mac更新成功！');

    }

}