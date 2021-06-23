<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
use App\Models\HostStatistical;
use App\Models\Vulnerability;
use App\Models\Es;
use App\Models\Host;

class HostStatisticalController extends Base
{
    protected $model;
    protected $vulmodel;
    protected $es;

    public function init()
    {
        parent::init();
        $this->model = new HostStatistical();
        $this->vulmodel = new Vulnerability();
        $this->es = new Es();
    }


    //操作系统类型分类
    public function getSensorSystemAction()
    {
        $data = $this->model->getSensorSystem();
        foreach ($data as $k => $v) {
            if ($v['name'] == '未知') {
                unset($data[$k]);
            }
        }
        if (count($data) == 11) {
            unset($data[10]);
        }
        $res = array_values($data);
        if(!empty($res)){
            jsonResult($res);
        }else{
            jsonResult([['name' => '暂无数据','value'=>0]]);
        }
    }

    //卷包车间
    public function getJuanBaoAction()
    {
        $data = $this->model->getJuanBao();
        jsonResult($data);
    }

    //制丝车间
    public function getZhiSiAction()
    {
        $data = $this->model->getZhiSi();
        jsonResult($data);
    }


    //能管车间
    public function getNengGuanAction()
    {
        $data = $this->model->getNengGuan();
        jsonResult($data);
    }

    //主机状态列表
    public function getHostAction()
    {
        $data = $this->model->getHost();
        jsonResult($data);
    }

    //资产服务监控
    public function getHostServiceAction()
    {
        $data = $this->model->getHostService();

        foreach ($data as $k => $v) {
            if (empty($v['service'])) {
                unset($data[$k]);
            }
        }
        $d = array_values($data);

        foreach ( $d as $key => $val) {
            $product = [];
            $port = [];
            foreach (json_decode($val['service'], 1) as $kk => $vv) {
                if (!empty($vv['product'])) {
                    array_push($product, $vv['product']);
                }

                if (!empty($vv['port'])) {
                    array_push($port, $vv['port']);
                }
            }
            $str_product = implode(',', $product);
            $str_port = implode(',', $port);

            $d[$key]['product'] = $str_product;
            $d[$key]['port'] = $str_port;
            unset($d[$key]['service']);
        }

        jsonResult($d);
    }

    //实时防护资产
    public function getHostVulListAction()
    {
        $redis = new \phpredis();

        if ($redis->get('vulnerabList123')) {
            $hosts = json_decode($redis->get('vulnerabList123'), 1);
        } else {
            $hosts = $this->vulmodel->getAllHost();
//排除自身采集器 平台
            $udpHost = $this->vulmodel->getUdpHost();

            foreach ($hosts as $a => $b){
                foreach ($udpHost as $c => $d){
                    if($b['ip'] == $d['ip']){
                        unset($hosts[$a]);
                    }
                }
            }
            $hosts = array_values($hosts);

            //过滤无系统无服务的  就不查了
//        foreach ($hosts as $k => $v) {
//            if (empty($v['value']) && empty($v['service'])) {
//                unset($hosts[$k]);
//            }
//        }
           
            foreach ($hosts as $key => $val) {

                $num = 0;

                if (empty($val['value']) && empty($val['service'])) {
                    $hosts[$key]['system_service_sum'] = $num;
                }


                //系统
                if ((!empty($val['value'])) && (strlen($val['value']) > 7)) {

                    $params1['query']['wildcard']['vuln_desc'] = "*" . $val['value'] . "*";
                    $params1['size'] = 0;

                    $params2['query']['wildcard']['vuln_name'] = "*" . $val['value'] . "*";
                    $params2['size'] = 0;

                    $data1 = $this->Format($this->es->query('andi-vuln-database', $params1));
                    $data2 = $this->Format($this->es->query('andi-vuln-database', $params2));

                    $data = $data1 + $data2;
                    $num += $data;

                    unset($params1);
                    unset($params2);
                    unset($data);

                } else {

                    $num += 0;

                }

                //服务
                if (!empty($val['service'])) {

                    $service = json_decode($val['service'], 1);

                    if ($service == null) {
                        $hosts[$key]['system_service_sum'] = $num;
                        $hosts[$key]['level'] = $this->level($num);
                        continue;
                    } else {
                        $params1['query']['bool']['should'] = [];
                        $params1['size'] = 0;
                        $params2['query']['bool']['should'] = [];
                        $params2['size'] = 0;
                        foreach ($service as $kk => $vv) {

                            if (!empty($vv['product'])) {

                                array_push($params1['query']['bool']['should'], ['wildcard' => ['vuln_desc' => '*' . $vv['product'] . '*']]);
                                array_push($params2['query']['bool']['should'], ['wildcard' => ['vuln_name' => '*' . $vv['product'] . '*']]);
                            }
                        }

                        //wildcard 不支持双字段查询
                        if (!empty($params1)) {

                            $data3 = $this->Format($this->es->query('andi-vuln-database', $params1));
                            $num += $data3;
                            unset($params1);

                        } else {
                            $num += 0;
                        }

                        if (!empty($params2)) {

                            $data4 = $this->Format($this->es->query('andi-vuln-database', $params2));
                            $num += $data4;
                            unset($params2);

                        } else {
                            $num += 0;
                        }
                    }

                } else {
                    $num += 0;

                }
                $hosts[$key]['system_service_sum'] = $num;
                $hosts[$key]['level'] = $this->level($num);
                $hosts[$key]['sensor'] = $this->vulmodel->getSensor($val['host_id'])?$this->vulmodel->getSensor($val['host_id']):'';
            }

            //排序
            $flag = array();
            foreach ($hosts as $v) {
                $flag[] = $v['system_service_sum'];
            }
            array_multisort($flag, SORT_DESC, $hosts);
           $redis->set('vulnerabList123', json_encode($hosts, 256), 0, 0, 180);
        }

        $res['total_num'] = count($hosts);
        $res['total_page'] = ceil($res['total_num'] / 50);
        $database = $this->split($hosts, 50);
        $page = input('post.page', 1);
        $res['list'] = $database[$page - 1];

        if (empty($res['list'])) {
            $res['total_num'] = 0;
            $res['total_page'] = 0;
            $res['list'] = [];
        }

        jsonResult($res);
    }

    //车间资产数量变化趋势
    public function hostNumAction()
    {
        $data = $this->model->hostNum();
        jsonResult($data);
    }

    private function Format($data)
    {
        return $data['hits']['total']['value'];
    }


    //等级
    private function level($num)
    {
        if ($num <= 10) {
            return '低';
        } elseif (10 < $num && $num <= 20) {
            return '中';
        } elseif ($num > 20) {
            return '高';
        }

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

}

?>