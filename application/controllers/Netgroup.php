<?php
use App\Models\NetGroup;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class NetgroupController extends Base
{
    protected $model = null;
    protected $uid=null;
    protected $rid=null;
    protected $num = 26;

	public function init()
    {
        parent::init();
        $this->model = new NetGroup();
        $this->uid =$_SESSION['uid'];
        $this->rid =$_SESSION['rid'];
        $this->checkAuth($this->num);
    }

//列表数据
    public function querylistAction()
    {
        $username= \Aes::decrypt(input('username'));
        $nickname = \Aes::decrypt(input('nickname'));
        $email   = \Aes::decrypt(input('email'));
        $mobile  = \Aes::decrypt(input('mobile'));
        $rolename  = \Aes::decrypt(input('rolename'));
        $page    = \Aes::decrypt(input('page',0));
        $loginip= \Aes::decrypt(input('loginip'));
        $pagesize= \Aes::decrypt(input('pagesize',10));
        $start   = \Aes::decrypt(input('start'));
        $end     = \Aes::decrypt(input('end'));
        $where = array();        
		if ($username<>''){
			$where['a.username'] = array('like',"%{$username}%");
        }
        if ($nickname<>''){
            $where['a.nickname'] = array('like',"%{$nickname}%");
        }
        if ($email<>''){
			$where['a.email'] = array('like',"%{$email}%");
        }
        if ($mobile<>''){
			$where['a.mobile'] = array('like',"%{$mobile}%");
		}
        if ($loginip<>''){
            $where['a.loginip'] = $loginip;
        }
        if ($start<>''){
			$where['a.birthday'] = array('egt',$start);
		}
        if ($end<>''){
			$where['a.birthday'] = array('elt',$end);
		}
        if ($rolename<>''){
            $where['d.rolename'] = array('like',"%{$rolename}%");
        }



        
        

        $model = $this->model;
        $Menu = new Menu();
        $datalist['total'] = $model->alias('a')
        ->join('left join user_reference b on a.id=b.uid left join user_role d on a.rid=d.id')->where($where)->count();
        $datalist['list'] = $model->field('a.id,a.group_id,a.username,a.nickname,a.email,a.mobile,a.avatar,a.level,a.gender,FROM_UNIXTIME(a.logintime) logintime,a.status,a.birthday,FROM_UNIXTIME(a.createtime) createtime,a.loginip,a.status,b.menus,b.content,b.menuids,b.id userreferenceid,d.rolename,a.rid')
        ->alias('a')
        ->join('left join user_reference b on a.id=b.uid left join user_role d on a.rid=d.id')
        ->where($where)
        ->order('a.id desc')
        ->page($page, $pagesize)
        ->select();
        //echo $model->getLastSql();die;
      
        //$res = encryptcode(json_encode($datalist,1));
        jsonResult($res);
    }

	public function saveAction()
    {
        try {
            $model = $this->model;
            if (!$model->create()){
                $errtips = $model->getError();
                jsonError('002',$errtips);
            }else{
                $id = input('id');
                if ($id>0){
                    $result = $model->where($model->getPk()." ='{$id}' ")->save();
                } else {
                    $result = $model->add();
                }
              
                if (!is_numeric($result)){
                    $this->logger(37);
                    jsonError('保存用户失败');
                } else {
                    $this->logger(36);
                    jsonResult('保存用户成功');
                }
            }
        } catch(Exception $e){
            $msg = $e->getMessage();
            $this->logger(37,$msg);
            jsonResult($e->getCode(),$e->getMessage());
        }


    }
}