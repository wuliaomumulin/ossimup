<?php
use App\Models\User;
use App\Models\Group;
use App\Models\Menu;
use App\Models\Userreference;

/* @name UserController
 * @desc 用户管理
 * @see http://127.0.0.1/Index/User/index
 */
class UserController extends Base
{
    protected $model = null;
    protected $uid=null;
    protected $rid=null;
    
    public function init()
    {
        parent::init();
        $this->model = new User();
        $this->uid =$_SESSION['uid'];
        $this->rid =$_SESSION['rid'];
    }

    /* 
    *  获取公司和厂站的接口
    */
    public function getsensorAction()
    {       



        $model = $this->model;
        $rs['list']=$model->getSensorV2();
 
        $rs['count'] = @sizeof($datalist);
        jsonResult($rs);
    }

    //列表数据
    public function querylistAction()
    {
        $username= \Aes::decrypt(input('username'));
        $nickname = \Aes::decrypt(input('nickname'));
        $email   = \Aes::decrypt(input('email'));
        $mobile  = \Aes::decrypt(input('mobile'));
        $comname  = \Aes::decrypt(input('comname'));
        $page    = \Aes::decrypt(input('page',0));
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
        if ($comname<>''){
            $where['c.comname'] = array('like',"%{$comname}%");
        }
        if ($start<>''){
			$where['a.birthday'] = array('egt',$start);
		}
        if ($end<>''){
			$where['a.birthday'] = array('elt',$end);
		}

        //过滤超级管理员
        if($_SESSION['rid'] <>  1) {
           $where['a.id'] = array('neq',1);
        }

        
        

        $model = $this->model;
        $Menu = new Menu();
        $count = $model->alias('a')
        ->join('left join user_reference b on a.id=b.uid left join user_role d on a.rid=d.id')->where($where)->count();
        $datalist = $model->field('a.id,a.group_id,a.username,a.nickname,a.email,a.mobile,a.avatar,a.level,a.gender,FROM_UNIXTIME(a.logintime) logintime,a.status,a.birthday,FROM_UNIXTIME(a.createtime) createtime,a.loginip,a.status,b.menus,b.content,b.menuids,b.id userreferenceid,d.rolename,a.rid')
        ->alias('a')
        ->join('left join user_reference b on a.id=b.uid left join user_role d on a.rid=d.id')
        ->where($where)
        ->order('a.id desc')
        ->page($page, $pagesize)
        ->select();
        //echo $model->getLastSql();die;
        if ($datalist){
             foreach($datalist as &$item){
                     $item['menus'] = unserialize($item['menus']);
                     $item['content'] = unserialize($item['content']);
                     if(!empty($item['content'][0]['name'])){
                        $temp=array_column($item['content'],'name');
                        if($temp) $item['showcontent'] = implode(',',$temp);
                    }
                 if(!empty($item['menuids'])){
                     $tem=$Menu->field('name')->where(['id'=>['in',$item['menuids']]])->select();
                 }
                 $in = '';
                 if(!empty($tem)){
                     foreach($tem as $a) $in .= $a['name'].',';
                    $in=substr($in,0,strlen($in)-1);
                 }
                 $item['showmenus'] = $in;
             }
         }
        $res = encryptcode(json_encode($datalist,1));
        echo resultstatus('0','数据列表',$res,$count);
    }

    //信息添加
    public function editAction()
    {
        $id = input('id',0);
        $model = $this->model;
        $info = $model->find($id);
        $model = new Group();
        $grouplist = $model->where("`status`='1'")->getField('id,title');
        $this->getView()->assign("info", $info);
        $this->getView()->assign("grouplist", $grouplist);
        $this->getView()->display('user/edit.html');
    }

    public function saveAction()
    {
        try {
            $model = $this->model;
            if (!$model->create()){
                $errtips = $model->getError();
                jsonError('002',$errtips);
            }else{
                $id = decryptcode(input('id'));
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

    public function destroyAction()
    {
        $id  = decryptcode(input('id',0));
        $model = $this->model;
        $res = $model->delete($id);
        if(!is_numeric($res)){
            $this->logger(41);
            jsonError('删除用户失败');
        }else{
            $this->logger(40);
            jsonResult('删除用户成功');
        }

    }

    public function authopenAction(){
        $info = input('post.');
        //var_dump($info);
        $user = new User();
        if($info['status'] != 1){
            $user->execute("update yaf_user set status = 1 where id = {$info['id']}");
            $this->logger(42);
            jsonResult('权限开通');
        }else{
            $user->execute("update yaf_user set status = 0 where id = {$info['id']}");
            $this->logger(43);
            jsonResult('权限关闭');
        }

    }


    /**
     * @abstract 修改主题 修改 默认首页
     * @return [type] [description]
     */
    public function saveDataAttrsAction(){

        $type = input('get.type','theme');//theme  主题  DEFAULT_ROULTER 默认首页

        $types = ['theme','DEFAULT_ROULTER','logo'];

        if (!in_array($type, $types)) jsonError('无效的参数');

        $params = input('get.params');//theme1 
       
        if(!Tools::isEmpty($_FILES['file'])){

            $upload = new spUploadFile();

            $path = '/var/www/public/upload/';

            $url = 'user/logo/'.date('Y-m-d').'/';

            if(!file_exists($path.$url)){
              mkdir($path.$url,'0777');
              chmod($path.$url,0777);
            }

           $url = $upload->upload_file($_FILES['file'],'img|png|jpg|jpeg',$url);  

            if($upload->errmsg==''){

                $params = $upload->uploaded;

            }else{
                jsonError($upload->errmsg);
            }   

        }

         $rs = [];

         if (!empty($params)) {

            $user_attrs = $this->model->field("user_attrs")->where('id = '.$_SESSION['uid'])->find();

            if ($user_attrs['user_attrs'] == null || $user_attrs['user_attrs'] == 'null' || empty($user_attrs['user_attrs'])) {
   
                $rs = $this->model->where('id = '.$_SESSION['uid'])->save(["user_attrs"=>json_encode([$type=>$params])]);

            }else{

                $rs = $this->model->execute("UPDATE yaf_user SET user_attrs = json_set(user_attrs,'$.".$type."','".$params."') where id = ".$_SESSION['uid']);
            }
         }
         jsonResult($this->model->getLastSql());
    }

    /**
    * 修改当前用户信息
    */
    public function setUserInfoAction(){

        if($this->getRequest()->isGet()){

            $uid = $_SESSION['uid'];
            $rs = $this->model->field('nickname,email,mobile')->where(['id'=>$uid])->find();
            $rs = encryptcode(json_encode($rs,1));
            jsonResult($rs);

        }
        if($this->getRequest()->isPost()){

            $old = decryptcode(input("post.old_password",''));
            $new = decryptcode(input('post.password',''));
            $nickname = decryptcode(input('post.nickname',''));
            $email = decryptcode(input('post.email',''));
            $mobile = decryptcode(input('post.mobile',''));
            if(!\Tools::isEmpty($nickname)) $data['nickname']=$nickname;
            if(!\Tools::isEmpty($email)) $data['email']=$email;
            if(!\Tools::isEmpty($mobile)) $data['mobile']=$mobile;


            $uid = $_SESSION['uid'];

            //验证老的密码的有效性
            if(!\Tools::isEmpty($old) and !\Tools::isEmpty($new)) {
                $info = $this->model->field('password')->where(['id'=>$uid])->getField('password');
                if(mymd5($old) != $info) jsonError('原密码输入有误');
                $data['password'] = mymd5($new);
            }

            $rs = $this->model->where(['id'=>$uid])->data($data)->save();

            jsonResult($rs);
        }

    }
    /**
    * 验证密码
    */
    public function verifyPasswordAction(){
        
        $login_num = $this->model->get_login_num();
        
        if($login_num < 5){
            $userpwd = \Aes::decrypt(input('post.userpwd','')); 
            $uid = $_SESSION['uid'];
            if(empty($userpwd)){
                jsonError('无效的参数');
            }

            $result = $this->model->where(['id'=>$uid])->getField('password');

            if($result && $result==mymd5($userpwd)){
                jsonResult('密码输入正确');
            }else{
                $this->model->set_login_num($_SESSION['username'],$login_num);
                jsonError('密码有误');
            }
        }else{
            jsonError('请休息一下,想一想密码再操作');
        }
        
    }

    /**
    * 重置密码
    * @param id
    */
    public function resetPasswordAction(){
        
        $login_num = $this->model->get_login_num();
        
        if($login_num < 5){
            $id = \Aes::decrypt(input('post.id','')); 
            if(empty($id)){
                jsonError('无效的参数');
            }

            $userpwd = mymd5('Admin@123');
            $result = $this->model->where(['id'=>$id])->setField('password',$userpwd);

            if(is_numeric($result)){
                jsonResult('密码已经重置');
            }else{
                $this->model->set_login_num($_SESSION['username'],$login_num);
                jsonError('重置密码失败');
            }
        }else{
            jsonError('操作太频繁，缓一缓再操作');
        }
        
    }

}
