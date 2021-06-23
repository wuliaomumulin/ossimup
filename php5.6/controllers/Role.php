<?php
use App\Models\Role;
/**
 * @name RoleController
 * @desc 角色管理
 * @see 
 */
class RoleController extends Base
{
    protected $model = null;
    protected $uid=null;
    protected $group_id=null;
    
    public function init()
    {
        parent::init();
        $this->model = new Role();
        $this->uid =$_SESSION['uid'];
        $this->group_id =$_SESSION['group_id'];
    }

    //列表页面
    public function querylistAction()
    {
        $model = $this->model;
        $rolename= \Aes::decrypt(input('rolename'));
        $page    = \Aes::decrypt(input('page',0));
        $pagesize= \Aes::decrypt(input('pagesize',10));
        if ($rolename<>''){
            $where['a.rolename'] = array('like',"%{$rolename}%");
        }
        //过滤超级管理员
        if($_SESSION['rid'] <>  1) {
           $where['a.id'] = array('eq',3);
        }

        $datalist=$model->getList($where,$page,$pagesize);
        $res = encryptcode(json_encode($datalist,1));
        $count=$model->getCount($where);
        echo resultstatus('0','数据列表',$res,$count);
    }
    //信息保存
    public function saveAction()
    {
        try {
            $model = $this->model;
            if (!$model->create()){   
                $errtips = $model->getError();
                echo resultstatus('002',$errtips);                
            }else{
                $id = \Aes::decrypt(input('id'));

                if ($id>0){
                    $result = $model->where($model->getPk()." ='{$id}' ")->save();
                } else {
                        $result = $model->add();
                }

                if ($result===false){
                    $this->logger(89);

                    jsonError('角色分配失败');
                } else {
                    $this->logger(88);
                    jsonResult([],'角色分配成功');
                }                 
            }
        } catch(Exception $e){
            $msg = $e->getMessage();
            echo resultstatus($e->getCode(),$e->getMessage());            
        }
        $this->logger($msg);
    }
    
    //信息删除
    public function destroyAction()
    {
        $id  = \Aes::decrypt(input('id',0));
        $model = $this->model;
        $res = $model->delete($id);
        $this->logger(95);
        jsonResult([],'删除角色成功');
    }
}
