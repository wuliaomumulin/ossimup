<?php

use App\Models\User;
use App\Models\LogMsg;
use App\Models\Log;

/**
 * @name LoginController
 * @desc 用户登录
 * @see http://127.0.0.1/Index/Login/index
 */
class LoginController extends \Yaf\Controller_Abstract
{


    public function indexAction()
    {
        $uid = intval($_SESSION['uid']);
        if ($uid > 0) {
            echo resultstatus(0, '已登录');
        } else {
            echo resultstatus(1, '未登录');
        }
    }

    public function checkloginAction()
    {
        $Aes = new Aes();
        $username = $Aes->decrypt(input("username", ''));
        $userpwd = $Aes->decrypt(input("userpwd", ''));
        $vcode = input("vcode", '');
        $model = new User();

        $redis = new phpredis();

        $key = get_client_ip() . $username;

        $login_num = $redis->get($key) ? $redis->get($key) : 0;

        try {
            $result = $model->checkLogin($username, $userpwd, $vcode, $login_num);
            $result['token'] = getToken();
            if ($_SESSION['uid'] > 0) {
                if (isset($login_num) && $login_num > 0) {
                    $redis->del($key); // 登陆成功之后清除失败记录
                }
                self::logger(81);
                echo resultstatus(0, '登录成功', $Aes->encrypt(json_encode($result)));
            }
        } catch (Exception $e) {
            //日志记录
            self::logger(81);
            //$e->getCode() 等于 4为账户或密码错误 只有此类型的错误会被记录
            if ($e->getCode() == 4 || $e->getCode() == 3) {
                ++$login_num;
                //小于5次的保留1Min
                if ($login_num < 5) {
                    $redis->set($key, $login_num, 0, 0, 60);
                    // 等于5的话   保留20MIN
                } elseif ($login_num == 5) {
                    $redis->set($key, 5, 0, 0, 900);
                }

            }

            echo resultstatus($e->getCode(), $e->getMessage(), array(), $login_num);
        }
        return false;
    }

    //验证码图片
    public function captchaimgAction()
    {
        $captcha = new Captcha();
        $captcha->generate(4, 136, 38);
        return false;
    }

    //退出登录
    public function logoutAction()
    {

        self::logger(82);
        $_SESSION = array();
        session_unset();
        session_destroy();
        echo resultstatus(0, '退出成功');
    }

    public function loginStatusAction()
    {
        $res = self::logger(83);
        if ($res !== false) {
            jsonResult();
        } else {
            jsonError();
        }
    }

    /**
     * 记录logger
     * remark 记录信息 (保存信息成功|删除信息成功)
     */
    protected function logger($code, $info = '')
    {
        $logMsg = new LogMsg();
        $status = $logMsg->getStatus($code);
        if ($status['status'] == 1) {
            $msg = $logMsg->getSysLogMsg($code);
            $log = new Log();
            $da = array('user_name' => $_SESSION['username'], 'log_event' => $info == '' ? $msg['name'] : $info, 'log_ip' => get_client_ip(), 'remark' => $msg['msg']);
            return $log->syslogadd($da);
        }

    }

}
