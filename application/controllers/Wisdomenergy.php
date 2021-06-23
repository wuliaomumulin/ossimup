<?php

use App\Models\Wisdomenergy;
use App\Models\ZhnySmartCollect;
use App\Models\Es;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);


class WisdomenergyController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;

    public function init()
    {
        parent::init();
        $this->config = \Yaf\Registry::get("config");
        $this->wisdomenergy = new Wisdomenergy();
        $this->es = new Es();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色

    }

    //获取菜单
    public function GetMenuAction()
    {
        $data = $this->wisdomenergy->GetMenu();
        $tree = Tree::instance()->formatTree($data);
        jsonResult($tree);
    }

    //保存模板
    public function SaveMenuAction()
    {

        $param = input('post.');
        if (!\Tools::isEmpty($param['name']) && !\Tools::isEmpty($param['type'])) {

            $mapping = [
                '机组1' => 1,
                '机组2' => 2,
                '机组3' => 3,
                '机组4' => 4,
                '机组5' => 5,
                '机组6' => 6,
                '机组7' => 7,
                '机组8' => 8,
                '机组9' => 9,
                '机组10' => 10,
            ];

            $param['id'] = $mapping[$param['name']];
            $param['istemplate'] = 0;

            $param['menus'] = $this->wisdomenergy->where(['istemplate' => 1, 'type' => $param['type']])->getField('menus');

            // $param['detail'] = $this->wisdomenergy->where(['istemplate' => 1,'type' => $param['type']])->getField('menus');
            $res = $this->wisdomenergy->CreateItem($param);
            if ($res != 0) {
                $this->logger(96);
                jsonResult();
            }
            $this->logger(97);
            jsonError();
        } else {
            $this->logger(97);
            jsonError('信息不能为空');
        }
    }

    //保存模板
    public function saveConfigAction()
    {
        $param['id'] = input('post.id');
        $param['menus'] = input('post.menus');
        if (!\Tools::isEmpty($param['menus'])) {

            //先查再改
            $old_menus = $this->wisdomenergy->GetMenuData($param['id']);

            $param['menus'] = json_encode($this->FormatTree(json_decode(html_entity_decode($param['menus']), 1)), 256);

            $res = $this->wisdomenergy->SaveMenu($param);

            if ($res >= 0) {
                //更改成功后 获取新增的指标
                $tags = $this->wisdomenergy->UpdateMenu($old_menus, $param);
                $data['vendor'] = 'MAGUS';
                $data['dev'] = $param['id'];
                $data['ts'] = date("Y-m-d H:i:s");

                $data['value'] = 0;
                foreach ($tags as $k => $v) {
                    $data['tag'] = $v;
                    $this->es->add('zhny_enterprise_report', $data);

                }

                $this->logger(96);
                jsonResult();
            }
            $this->logger(97);
            jsonError();
        } else {
            $this->logger(97);
            jsonError('信息不能为空');
        }
    }

    //保存模板数据
    public function SaveMenuDataAction()
    {
        $param = input('post.detail');
        if (!\Tools::isEmpty($param)) {
            $data = json_decode(html_entity_decode($param), 1);
            $tag = true;
            foreach ($data as $k => &$v) {
                $v['ts'] = date("Y-m-d H:i:s");
                $res = $this->es->add('zhny_enterprise_report', $v);
                if ($res == false) {
                    $tag = false;
                }
            }

            if ($tag == true) {
                //确定机组
                $dev = $data[0]['dev'];

                $params['query']['term']['dev'] = intval($dev);
                $params['aggregations']['tag']['terms']['field'] = 'tag';
                $params['aggregations']['tag']['terms']['size'] = 1000;
                $params['aggregations']['tag']['aggregations']['top']['top_hits']['size'] = 1;
                $params['aggregations']['tag']['aggregations']['top']['top_hits']['sort']['ts']['order'] = 'desc';

                $collect = $this->Format($this->es->query('zhny_smart_collect', $params));  //采集

                $datas = array_merge($data, $collect);  //上报，采集

                //找到当前机组需要展示的menu
                $menu = json_decode($this->wisdomenergy->GetMenuData($dev), 1);
                foreach ($menu as $key => &$val) {
                    if (!empty($val['children'])) {
                        foreach ($val['children'] as $kk => &$vv) {
                            foreach ($datas as $k => &$v) {
                                if ($vv['name'] == $v['tag']) {
                                    $vv['value'] = $v['value'];
                                }
                            }
                        }
                    }
                }

                $this->logger(98);
                jsonResult($menu);
            }
            $this->logger(99);
            jsonError();
        } else {
            $this->logger(99);
            jsonError('参数错误');
        }
    }


    /**
     * 追加时间
     */


    private function appendTs($param)
    {
        $param = json_decode($param, true);
        $id = input('post.id');

        $ZhnySmartCollect = new ZhnySmartCollect();
        $collect = $ZhnySmartCollect->getAll();
        $tags = array_column($collect, 'tag');
        $tss = array_column($collect, 'ts');
        $values = array_column($collect, 'value');
        $devs = array_column($collect, 'devs');

        foreach ($param as $a => $b) {
            if (isset($b['children'])) {
                foreach ($b['children'] as $c => $d) {
                    if ($d['type'] == '自动采集') {
                        //echo implode(',',$tags).'---';
                        //根据数据更新周期，进行数据更新
                        //if($ind = array_search($d['name'],$tags) &&  && $this->gtTime($tss[$ind],$d['expire'])){
                        //if($ind = array_search($d['name'],$tags)){
                        $val = $this->wisdomenergy->table('zhny_smart_collect')->where(['tag' => $d['name'], 'dev' => $id])->getField('value');
                        if (!\Tools::isEmpty($val)) {
                            $param[$a]['children'][$c]['value'] = $val;
                        } else {
                            $param[$a]['children'][$c]['value'] = 0;
                        }
                        //if($ind = array_search($d['name'],$tags) && $id == $devs[$ind]){
                        // $param[$a]['children'][$c]['ts'] = $tss[$ind];//下回
                        // $param[$a]['children'][$c]['value'] = $this->wisdomenergy->table('zhny_smart_collect')->where(['tag' => $d['name']])->getField('value');
                        //}

                        // echo 'aaa-'.$d['name'].'bb';

                        //}
                    }
                }
            }
        }
        //echo json_encode($param);exit();

        return json_encode($param, 256);
    }

    /**
     * 判断大于当前时间多长时间，就会过期
     * @params time
     * @params expire
     */

    private function gtTime($time, $expire)
    {
        $current = strtotime(date('Y-m-d H:i:s')) + ($expire * 60);
        $reference = strtotime($time);
        return $current > $reference ? true : false;
    }

//获取模板信息

    public function GetMenuDataAction()
    {
        $params['query']['term']['dev'] = intval(input('get.id'));
        $params['aggregations']['tag']['terms']['field'] = 'tag';
        $params['aggregations']['tag']['terms']['size'] = 10000;
        $params['aggregations']['tag']['aggregations']['top']['top_hits']['size'] = 1;
        $params['aggregations']['tag']['aggregations']['top']['top_hits']['sort']['ts']['order'] = 'desc';
        //$params['query']['match_all'] = [];
        // echo json_encode($params,256);die;
        $report = $this->Format($this->es->query('zhny_enterprise_report', $params)); //上报
        $collect = $this->Format($this->es->query('zhny_smart_collect', $params));  //采集

        $data = array_merge($report, $collect);
        $menu = json_decode($this->wisdomenergy->GetMenuData(input('get.id')), 1);
        foreach ($menu as $key => $val) {
            if (!empty($val['children'])) {
                foreach ($val['children'] as $kk => $vv) {
                    foreach ($data as $k => $v) {
                        if ($vv['name'] == $v['tag']) {
                            $menu[$key]['children'][$kk]['value'] = $v['value'];
                        }
                    }
                }
            }
        }
        jsonResult($menu);
    }


//获取机组列表

    public function GetHostsAction()
    {
        $where['istemplate'] = 0;
        $data = $this->wisdomenergy->GetHosts($where);
        jsonResult($data);
    }

//获取树形

    public function FormatTree($data)
    {
        $pid = array_column($data, 'pid');
        $father_data = $this->wisdomenergy->GetFather(['id' => ['in', $pid]]);
        if (!empty($father_data)) {
            $res = array_merge($data, $father_data);
        }
        return Tree::instance()->formatTree($res);
    }

//删除模板

    public function DelMenuAction()
    {
        $id = input('get.id');
        if (!\Tools::isEmpty($id)) {

            //机组删除  删除（新增指标为0的数据）
            $menus = json_decode($this->wisdomenergy->GetMenuData($id), 1);
            $tags = [];
            foreach ($menus as $key => $val) {
                if (!empty($val['children'])) {
                    foreach ($val['children'] as $k => $v) {
                        if ($v['type'] == '企业上报') {
                            $tags[] = $v['name'];
                        }
                    }
                }
            }

            $res = $this->wisdomenergy->DelMenu($id);
            if ($res > 0) {
                if (!empty($tags)) {
                    $data['vendor'] = 'MAGUS';
                    $data['dev'] = $id;
                    $data['ts'] = date("Y-m-d H:i:s");
                    $data['value'] = 0;
                    foreach ($tags as $k => $v) {
                        $data['tag'] = $v;
                        $this->es->add('zhny_enterprise_report', $data);
                    }
                }

                $this->logger(100);
                jsonResult();
            }
            $this->logger(101);
            jsonError();
        } else {
            $this->logger(101);
            jsonError('参数错误');
        }

    }


    private function Format($data)
    {
        $res = [];
        foreach ($data['aggregations']['tag']['buckets'] as $k => $v) {
            $res[] = $v['top']['hits']['hits'][0]['_source'];
        }
        return $res;
    }
}