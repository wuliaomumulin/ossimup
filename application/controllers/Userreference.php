<?php
use App\Models\Userreference;
use App\Models\User;
use App\Models\Menu;
use App\Models\Log;

class UserreferenceController extends Base
{
    protected $model = null;
    protected $uid=null;
    protected $rid=null;
    
    public function init()
    {
        parent::init();
        
        $this->model = new Userreference();
        $this->uid =$_SESSION['uid'];
        $this->rid =$_SESSION['rid'];
    }
    //
    public function authsaveAction()
    {
        try {
            $model = $this->model;
            if (!$model->create()){
                $errtips = $model->getError();
                echo resultstatus('002',$errtips);
            }else{
                $id = decryptcode(input('id'));
                if ($id>0){
                    $result = $model->where($model->getPk()." ='{$id}' ")->save();
                   // jsonResult($model->getLastSql());
                }
                if ($result===false){
                    $this->logger(48);
                    echo resultstatus(1,'权限分配失败');
                } else {
                    $this->logger(47);
                    echo resultstatus(0,'权限分配成功');
                }
            }
        } catch(Exception $e){
            echo resultstatus($e->getCode(),$e->getMessage());
        }

    }
    
    /**
    * 获取当前用户的一些权限信息
    */
    public function menuidAction()
    {
        $uid=$this->uid;
        $rid=$this->rid;
        $rids = $this->role;
        $reference = new Userreference();
        $model = $this->model;
        $datalist = $reference->field('menuids,menus,content,menu_id')->where(['uid' => $uid])->find();
        jsonResult($datalist);
    }
}