<?php
/**
 *   获取一些不需要登录就可以得到的数据
 */

use App\Models\Systemconfig;
use App\Models\Configsystem;

class UnloginController extends Base
{

    public function init()
    {

        self::ipConstaint();

    }


    public function getconfigtitleAction()
    {

        $redis = new phpredis();

        $key = 'UnloginController-getconfigtitle';

        $rs = $redis->get($key);

        if ($rs) {
            jsonResult(json_decode($rs, 256));
        } else {
            $systemconfig = new Systemconfig();

            $resu = $systemconfig->field('value,name')->where(['name' => ['in', ['PLATFROM_TITLE', 'SUB_PLATFROM_TITLE']]])->select(['index' => 'name']);

            if (empty($resu)) jsonError('您无权访问本平台');

            foreach ($resu as $k => $v) {
                $rs[$k] = $v['value'];
            }

            $redis->set($key, json_encode($rs), 0, 0, 300);
            jsonResult($rs);
        }


    }

    //平台访问限制
    public function ipConstaint()
    {
        $ip = self::getIp();

        //同一个网段的才可访问
        $eth5 = "ifconfig eth5";
        exec($eth5, $result, $var);

        $arr = explode(" ", $result[1]);
        $ethIp = explode('.', $arr[9]);
        $ethIp = $ethIp[0] . '.' . $ethIp[1] . '.' . $ethIp[2];

        $fangIp = explode('.', $ip);
        $fangIp =  $fangIp[0] . '.' . $fangIp[1] . '.' . $fangIp[2];
        if($ethIp != $fangIp){
            jsonResult('您无权访问本平台!');
        }

        $ips = new Configsystem();
        $status = $ips->getIpLimit();
        $ip_list = $ips->getRequestList();
        if ($status['value'] == 1) {

            $arr = [];
            foreach ($ip_list as $k => $v) {
                array_push($arr, $v['ip']);
            }
            if (in_array($ip, $arr) === false) {
                $_SESSION = array();
                session_unset();
                session_destroy();
                jsonResult('您无权访问本平台!');
            }
        }
    }

    //获取访问者ip
    public function getIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }

    //登陆限制 、 超时等
    public function getTimeOutFailCountAction()
    {
        $ips = new Configsystem();
        $data = $ips->getTimeOutFailCount();
        jsonResult($data);
    }
}

?>