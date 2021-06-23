<?php
use App\Models\Systemconfig;
use App\Models\User;

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

        $add['name'] = strtoupper(decryptcode(input('key')));

        $add['value'] = decryptcode(input('value'));

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

    /**
    * 修改当前用户信息
    */
    public function setUserInfoAction(){

        $User = new User();

        if($this->getRequest()->isGet()){
            $uid = $this->authorization['id'];
            $rs = $User->field('nickname,email,mobile,usb_key')->where(['id'=>$uid])->find();
            $rs = encryptcode(json_encode($rs,1));
            jsonResult($rs);

        }
        if($this->getRequest()->isPost()){

            $old = decryptcode(input("post.old_password",''));
            $new = decryptcode(input('post.password',''));
            $nickname = decryptcode(input('post.nickname',''));
            $email = decryptcode(input('post.email',''));
            $mobile = decryptcode(input('post.mobile',''));
            $usb_key = decryptcode(input('post.usb_key',''));
            if(!\Tools::isEmpty($nickname)) $data['nickname']=$nickname;
            if(!\Tools::isEmpty($email)) $data['email']=$email;
            if(!\Tools::isEmpty($mobile)) $data['mobile']=$mobile;

            //usb_key的用户权限
            if(in_array($this->authorization['rid'],[1,2])){
                $data['usb_key'] = $usb_key;
            }

            if($this->authorization['username'] != $_SESSION['username']){
                jsonError('非法请求');
            }

            $uid = $this->authorization['id'];

            //验证老的密码的有效性
            if(!\Tools::isEmpty($old) and !\Tools::isEmpty($new)) {
                $info = $User->field('password')->where(['id'=>$uid])->getField('password');
                if(mymd5($old) != $info) jsonError('原密码输入有误');
                $data['password'] = mymd5($new);
                $data['updatetime'] = time();
            }
            $rs = $User->where(['id'=>$uid])->data($data)->save();


            (int)$rs > 0 ? jsonResult() : jsonError();
        }

    }
}