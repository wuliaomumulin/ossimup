<?php
use App\Models\Systemconfig;

class SystemconfigController extends Base
{
    protected $model = null;
    protected $uid=null;
    protected $rid=null;

    public function init()
    {   
        parent::init();
        $this->model = new Systemconfig();
        $this->uid =$_SESSION['uid'];
        $this->rid =$_SESSION['rid'];
    }

    /**
     * @abstract 规则列表数据
     * @author  王成
     * @return json
     */
    public function querylistAction()
    {
        $where = array();
        $where['status'] = ['neq',2];
        $where['name']    = input('name');//名称
        $where['value']= input('value');//内容
        $where['desc']= input('desc');//描述
        $where['status']      = input('status');//状态

        $field = '*';
        $page       = input('page', 0);
        $pagesize   = input('pagesize', 10);
        $where = array_filter($where,function($var){
            if(!empty($var)){
                return $var;
            }
        });

        if (!empty($search)) $where  = $this->model->getWhere($search);

        $datalist =  $this->model->getDataList($field,$where,'','a.id desc',$page,$pagesize,'');
        // 返回查询的结果
        jsonResult($datalist);
    }

    /**
     * @abstract 添加，修改规则
     * @author  王成
     * @return json
     */
    public function systemconfigaddAction()
    {   
   
       $id = input('id');
       $data['name'] = strtoupper(input('name'));
       $data['value'] = str_replace('&quot;', '"', input('value'));
       $data['desc'] = input('desc');
       $data['uid'] = $_SESSION['uid'];

       // 如果系统账号的添加权限 是管理员 则为系统导入
       if ($_SESSION['rid'] <= 3 ) {
            $data['type']  = 1;
        // 如果不是的话  则为用户导入
       }else{
            $data['type']  = 2;
       }

       if($id > 0){
            $res = $this->model->where('id = '.$id)->save($data);

       }else{
           $res = $this->model->add($data);
       }
        if ($res  == false) jsonError('操作失败！');
        jsonResult($res);
    }

    /**
     * @abstract 删除规则
     * @author  王成
     * @return json
     */
    public function systemconfigdelAction()
    {
        $id = input('id', 0);
        if(empty($id)){
            jsonError('ID不存在,非法操作');
        }
        $res = $this->model->where($this->model->getPk() . " in(".$id.") ")->delete();
        if(empty($res)){
            jsonResult([],'删除失败');
        }
        jsonResult([],'删除成功');
    }


    /**
     * @abstract 开启，关闭规则状态
     * @author  王成
     * @return json
     */

    public function statusupdateAction()
    {
        $id = input('id', 0);
        $data['status'] = 1-input('status');
        if(empty($id)){
            jsonError('ID不存在,非法操作');
        }
        $res = $this->model->where('id = '.$id)->save($data);
        if ($res  == false) jsonError('操作失败！');
        jsonResult($res,'操作成功');
    }

    public function setuserconfigAction(){

        $add['name'] = strtoupper(input('key'));

        $add['value'] = input('value');

        $config = $this->model->field('id,value')->where(['name'=>['eq',$add['name']],['user_id'=>['eq',$_SESSION['uid']]],'type'=>['eq',2]])->find();

        if (!empty($config['id']) && $config['value'] != $add['value']) {
            $this->model->where(['id'=>['eq',$config['id']]])->save(['value'=>$add['value']]);
        }elseif(empty($config['id'])){

            $add['user_id'] = $_SESSION['uid'];
            $add['type']    = 2;
            $add['status']  = 1;
            $this->model->add($add);
        }

        jsonResult();
    }
}