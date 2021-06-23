<?php
use App\Models\ThreatIntelligence;
use App\Models\Log;

class ThreatintelligenceController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
 
    public function init()
    {  
/*                ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);*/
        parent::init();
        
        $this->config = \Yaf\Registry::get("config");
        $this->model = new ThreatIntelligence();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
         
    }
    //列表
    public function querylistAction()
    {  
        $page                = input('page',1);
        $pagesize            = input('page_size',10);
        $where = self::prev();
        
        $datalist = $this->model->getList($where,$page,$pagesize);
        //exit($this->model->_sql());
        jsonResult($datalist);
    }

    /**
    * 删除
    */
    public function destroyAction(){
        $model = $this->model;

        $ids = input('post.id/s',0);//
        if(empty($ids)) jsonError('id参数缺失');
        $result = $model->where(['id' => [ 
            'exp',
            "in (unhex('{$ids}'))"
        ]])->delete();
        if(intval($result) > 0){
            $this->logger(29);
            jsonResult([],'删除情报成功');
        }else{
            $this->logger(30);
            jsonError('删除情报失败');
        }
    }

    /**
    * 详情
    */
    public function detailAction(){

        $id = input('post.id/s',0);//

        if(!empty($id)){

            $model = $this->model;

            $where = [
            'a.id' => ['exp',"=unhex('{$id}')"]
            ];

            $data = $model->alias('a')->field('hex(id) id,hex(a.event_id) event_id,hex(ctx) ctx,severity,family,detail,status,timestamp')->where($where)->find();

            jsonResult($data);

        }else{
            jsonError('没有唯一标识');
        }
        
    }

     /***
    * 前置方法，取出查询的唯一字段
    */
    public function prev(){
        $request = input('post.');
        unset($request['page'],$request['page_size']);

        //判断威胁类型
        if(isset($request['family'])){
            $where['a.family'] = ['like',"%{$request['family']}%"];
            unset($request['family']);
        }
        //判断ID
        if(isset($request['id'])){
            $where['a.id'] = ['exp',"=unhex('{$request['id']}')"];
            unset($request['id']);
        }

        //时间范围
        if(!\Tools::isEmpty($request['begindate']) and !\Tools::isEmpty($request['enddate'])){
            $where['a.timestamp'] = array(array('egt',$request['begindate']),array('elt',$request['enddate']));
            unset($request['begindate'],$request['enddate']);
        }   

        //默认等于匹配
        if(!Tools::isEmpty($request)){
            if(count($request) === 1){
                $where['a.'.key($request)] = current($request);
            }else{
                foreach ($request as $k => $v) {
                    $where['a.'.$k] = $v;
                }
            }        
        }


        return $where;

    }
    /**
    * 设置状态
    */
    public function setstatusAction(){
        $id = input('post.id/s');
        $status = input('post.status');

        if(Tools::isEmpty($id)){
            jsonError('没有唯一标识');
        }

        $result = $this->model->where("id = unhex('{$id}')")->setField('status',$status);
        if(is_numeric($result)){
            jsonResult('设置成功');
        }else{
            jsonError('设置失败');
        }   
    }
}