<?php

use App\Models\UdpSensor;
use App\Models\Host;
use App\Models\Userreference;
use App\Models\User;


class UdpsensorController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
    protected $num = 17;
 
    public function init()
    {
        parent::init();
        
        $this->config = \Yaf\Registry::get("config");
        $this->model = new UdpSensor();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        $this->checkAuth($this->num);
         
    }

    public function querylistAction()
    {  
       
        $page                = input('page/d',1);
        $pagesize            = input('page_size/d',10);
        $where = self::prev();

        $datalist = $this->model->getList($where,$page,$pagesize);
        jsonResult($datalist);
    }

    //详情
    public function detailAction()
    {  
        $where['ip'] = input('ip/s','');//32位
       
        if(Tools::isEmpty($where['ip']) OR !filter_var($where['ip'],FILTER_VALIDATE_IP)){
            jsonError('无效的参数:资产ip');
        }
        $datalist = $this->model->getOne($where);
        jsonResult($datalist);
    }


    /***
    * 前置方法，取出查询的唯一字段
    */
    public function prev(){
        $request = input('post.');
        unset($request['page'],$request['page_size'],$request['begindate'],$request['enddate']);

        if(!Tools::isEmpty($request['name'])){
            $where['a.name'] = ['like',"%".$request['name']."%"];
            unset($request['name']);
        }


        if(!Tools::isEmpty($request)){
            $where['a.'.key($request)] = current($request);
        }

        return $where;
    }



    //信息保存
    public function saveAction()
    {
        try {
            $model = $this->model;
            if (!$model->create()){   
                $errtips = $model->getError();

                 jsonError($errtips);                
            }else{
                $ip = input('ip');
                $name = input('name');

                //判断name是否存在于udp_sensor表中
                $count = $model->where("name ='{$name}'")->count();

                //self::saveAssetHook();

                if ($count>0){
                    $result = $model->where("name ='{$name}'")->save();
                } else {
                        $result = $model->add();
                }

                if ($result===false){
                    $msg = '保存信息失败';
                    $this->logger($msg);
                    jsonError($msg);
                }else{
                    $msg = '保存信息成功';
                    $this->logger($msg);
                    jsonResult([],$msg);
                }                 
            }
        } catch(Exception $e){
            $msg = $e->getMessage();
            $this->logger($msg);
            jsonError($msg);            
        }
    }

    /**
    * 删除UdpSensors
    */
    public function destroyAction(){

        $model = $this->model;

        $ids = input('post.ip/s',0);//
        if(empty($ids)) jsonError('Ip参数缺失');

        $result = $model->where(['ip'=>$ids])->delete();
        if(intval($result) > 0){
            $msg = '删除信息成功';
            $this->logger($msg);
            jsonResult([],$msg);
        }else{
            jsonError('删除信息失败');
        }
    }

    /**
    * 拥有的所有类型
    */
    public function typeAction(){
        $datalist = $this->model->getTypes();
        jsonResult($datalist);
    }

    /**
    * 保存资产
    */
    private function saveAssetHook(){
        //大的分组只会是11

        //将资产IP、站产类型、名称添加一张表里
        $data = $this->model->data();
        $data['id'] = uuid();
        $data['ctx'] = $this->config->application->ctx;
        $data['hostname'] = $data['name'];
        $data['asset'] = 2;
        $data['fqdns'] = '';
        $data['alert'] = 0;
        $data['persistence'] = 0;
        $data['nat'] = 0;
        $data['rrd_profile'] = 0;
        $data['type'] = [11,$data['subtype']];
        $data['lat'] = 0;
        $data['lon'] = 0;
        $data['icon'] = '';
        $data['country'] = '';
        $data['external_host'] = 0;
        $data['mac'] = '';
        $data['interface'] = '';
        $Host = new Host();
        $result = $Host->saveHost($data);
       // var_dump($result);exit();
    }

    /**
    * 删除资产Hook
    */
    private function destroyAssetHook(){

    }
    /*
        更新频率
    */
    public function updatefrepAction(){
        $redis = new \phpredis();
        $key = $_SESSION['uid'] . '-'. __METHOD__;


        if($this->getRequest()->isPost()){
            $freq = input('post.freq',300000);
            $redis->set($key,$freq);
            jsonResult($freq);
        }
        if($this->getRequest()->isGet()){
            if ($result = $redis->get($key)) {
                jsonResult($result);
            } else {
                jsonResult(300000);
            }
        }
        
    }

}