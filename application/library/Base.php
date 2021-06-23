<?php
use App\Models\Log;
use App\Models\Configsystem;
use App\Models\LogMsg;
use App\Models\User;
/**
 * @name Base
 * @desc 基础公共类
 */
class Base extends \Yaf\Controller_Abstract
{

    //存储user的authorization
    use traits_User;

    public function init()
    {   
        
        self::ipConstaint();
        //用户登录检测
       if (intval($_SESSION['uid'])===0){
           echo resultstatus(0,'未登录');
           exit;
       }

       self::_initialize();

       if(!self::checkUserName()){
           echo resultstatus(0,'该用户被强制下线');
           exit;
       }
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
                $id = input('id');
                if ($id>0){
                    $result = $model->where($model->getPk()." ='{$id}' ")->save();
                } else {
                        $result = $model->add();
                }

                if ($result===false){
                    $msg = '保存信息失败';
                    jsonError($msg);
                } else {
                    $msg = '保存信息成功';
                    jsonResult([],$msg);
                }                 
            }
        } catch(Exception $e){
			$msg = $e->getMessage();
            echo resultstatus($e->getCode(),$e->getMessage());            
        }
        $this->logger($msg);
    }

    //信息排序修改
    public function changeAction()
    {
        $id = input('id',0);
        $field = input('field');
        $value = input($field);
        $model = $this->model;
        $model->$field = $value;
        $re = $model->where(" id='{$id}' ")->save();
        if($re){
            $msg = '修改信息成功';
            jsonResult([],$msg);
        } else {
            $msg = '修改信息失败';
            jsonError($msg);
        }

        $this->logger($msg);
    }
    
    //信息删除
    public function destroyAction()
    {
        $id  = input('id',0);
        $model = $this->model;
        $res = $model->delete($id);
        $msg = '删除信息成功';
        $this->logger($msg);
        jsonResult([],$msg);
    }

    //文件上传
    public function uploadAction()
    {
        $upload = new spUploadFile();
        $url = $upload->upload_file($_FILES['file'],"jpg|png|gif",'');        
        $data = ['src'=>$url];
        echo resultstatus(0,'上传文件成功',$data);
    }

    //平台访问限制
    public function ipConstaint()
    {
        $ip = self::getIp();
        $ips = new Configsystem();
        $status = $ips->getIpLimit();
        $ip_list = $ips->getRequestList();
        if($status['value'] == 1){
            $arr = [];
            foreach ($ip_list as $k => $v){
                array_push($arr,$v['ip']);
            }
            if(in_array($ip,$arr) === false){
                $_SESSION = array();
                session_unset();
                session_destroy();
                jsonError('您无权访问本平台!');
            }
        }
    }

    //获取访问者ip
    public function getIP(){
        if (isset($_SERVER)){
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }

    /**
    * 记录logger
    * remark 记录信息 (保存信息成功|删除信息成功)
    */
    protected function logger($code,$info = ''){
        $logMsg = new LogMsg();
        $status = $logMsg->getStatus($code);
        if($status['status'] == 1){
            $msg = $logMsg->getSysLogMsg($code);
            $log = new Log();
            $da = array('user_name'=>$_SESSION['username'],'log_event'=>$info == ''?$msg['name']:$info,'log_ip'=>get_client_ip(),'remark'=>$msg['msg']);
            $log->syslogadd($da);
        }

    }

    protected function checkUserName()
    {
        $user = @new User();
        $count = $user->where(['username' =>$_SESSION['username']])->count();
        if($count > 0){
            return true;
        }
        return false;
    }

    protected function checkAuth($id)
    {

        $auth = $this->menusids();
        if(strstr($auth,(string)$id) === false){
            jsonError('您无权访问!');
        }
    }

}
