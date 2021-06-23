<?php
namespace App\Models;

class Model extends \Model
{
	protected $tablePrefix = 'yaf_';
	
	function getuid() {
		return $_SESSION['uid'];
	}
	//获取当前时间戳
	function gettime() {
		return time();
	}
	
	//获取当前系统时间
	function getdatatime() {
		return date('Y-m-d H:i:s');
	}
    
    //获取当前系统日期
	function getdata() {
		return date('Y-m-d');
	}
	//获取13位时间戳
	function get13time($num=13){
		list($t1,$t2) =explode(' ',microtime());
		return $t2.ceil($t1*pow(10,substr($num,1,1)));
	}
	//检测是否为数字   数组/字符串
	function checkInt($params){
	
		if (is_array($params) && !empty($params)) {
            foreach ($params as $k => $v) {
                if (!is_numeric($v))  jsonError('无效的参数:'.$v);
            }
        }else{
              if (!is_numeric($params))  jsonError('无效的参数:'.$params);
        }

        return TRUE;
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

                throw new \Exception("您无权访问本平台",'004');
            
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
   // 回调方法 初始化模型
    protected function _initialize() {

        $this->redis = new \phpredis();

    }


/*
* 防止xss注入的函数，过滤掉那些非法的字符,提高sql安全性，同时也可以过滤XSS的攻击。
*
xss过滤事件
foreach($request as $k => $v) $request[$k] = $this->model->xss_filter($v);

//xss过滤事件
$ids = $this->model->xss_filter($ids);
*/
public function xss_filter($str)
{
    if (empty($str)) return false;
    $str = strtolower(htmlspecialchars($str));
    $str = str_replace( '/', "", $str);
    $str = str_replace( '"', "", $str);
    $str = str_replace( '(', "", $str);
    $str = str_replace( ')', "", $str);
    $str = str_replace( 'CR', "", $str);
    $str = str_replace( 'ascii', "", $str);
    $str = str_replace( 'ASCII 0x0d', "", $str);
    $str = str_replace( 'LF', "", $str);
    $str = str_replace( 'ASCII 0x0a', "", $str);
    //$str = str_replace( ',', "", $str);
    $str = str_replace( '%', "", $str);
    //$str = str_replace( ';', "", $str);
    $str = str_replace( 'eval', "", $str);
    $str = str_replace( 'open', "", $str);
    $str = str_replace( 'sysopen', "", $str);
    $str = str_replace( 'system', "", $str);
    $str = str_replace( '$', "", $str);
    $str = str_replace( "'", "", $str);
    $str = str_replace( 'ASCII 0x08', "", $str);
    $str = str_replace("&gt", "", $str);
    $str = str_replace("&lt", "", $str);
    $str = str_replace("<SCRIPT>", "", $str);
    $str = str_replace("</SCRIPT>", "", $str);
    $str = str_replace("<script>", "", $str);
    $str = str_replace("</script>", "", $str);
    $str = str_replace("select","",$str);
    $str = str_replace("join","",$str);
    $str = str_replace("union","",$str);
    $str = str_replace("and","",$str);
    $str = str_replace("or","",$str);
    $str = str_replace("where","",$str);
    $str = str_replace("insert","",$str);
    $str = str_replace("delete","",$str);
    $str = str_replace("update","",$str);
    $str = str_replace("version","",$str);
    $str = str_replace("like","",$str);
    $str = str_replace("drop","",$str);
    $str = str_replace("DROP","",$str);
    $str = str_replace("create","",$str);
    $str = str_replace("modify","",$str);
    $str = str_replace("order","",$str);
    $str = str_replace("if","",$str);
    $str = str_replace("else","",$str);
    $str = str_replace("elseif","",$str);
    $str = str_replace("rename","",$str);
    $str = str_replace("alter","",$str);
    $str = str_replace("cas","",$str);
    $str = str_replace("&","",$str);
    $str = str_replace(">","",$str);
    $str = str_replace("<","",$str);
    $str = str_replace(" ",chr(32),$str);
    $str = str_replace(" ",chr(9),$str);
    $str = str_replace("    ",chr(9),$str);

    $str = str_replace("&",chr(34),$str);

    $str = str_replace("'",chr(39),$str);
    $str = str_replace("<br />",chr(13),$str);
    $str = str_replace("''","'",$str);
    $str = str_replace("css","'",$str);
    $str = str_replace("CSS","'",$str);
    $str = str_replace("<!--","",$str);
    $str = str_replace("convert","",$str);
    $str = str_replace("md5","",$str);
    $str = str_replace("passwd","",$str);
    $str = str_replace("password","",$str);
    $str = str_replace("../","",$str);
    $str = str_replace("./","",$str);
    $str = str_replace("array","",$str);
    $str = str_replace("or 1='1'","",$str);
    $str = str_replace(";set|set&set;","",$str);
    $str = str_replace("`set|set&set`","",$str);
    $str = str_replace("--","",$str);
    $str = str_replace("or","",$str);
    $str = str_replace("*","",$str);
    //$str = str_replace("-","",$str);
    $str = str_replace("+","",$str);
    $str = str_replace("/","",$str);
    $str = str_replace("=","",$str);
    $str = str_replace("'/","",$str);
    $str = str_replace("-- ","",$str);
    $str = str_replace(" -- ","",$str);
    $str = str_replace(" --","",$str);
    $str = str_replace("{","",$str);
    $str = str_replace("}","",$str);
    $str = str_replace("-1","",$str);
    //$str = str_replace(".","",$str);
    $str = str_replace("response","",$str);
    $str = str_replace("write","",$str);
    $str = str_replace("|","",$str);
    $str = str_replace("`","",$str);
    $str = str_replace(";","",$str);
    $str = str_replace("from","",$str);
    $str = str_replace("table","",$str);
    $str = str_replace("etc","",$str);
    $str = str_replace("root","",$str);
    $str = str_replace("//","",$str);
    $str = str_replace("!=","",$str);
    $str = str_replace("$","",$str);
    $str = str_replace("&","",$str);
    $str = str_replace("&&","",$str);
    $str = str_replace("==","",$str);
    $str = str_replace("#","",$str);
    //$str = str_replace("@","",$str);
    $str = str_replace("mailto:","",$str);
    $str = str_replace("CHAR","",$str);
    $str = str_replace("char","",$str);
    return $str;
}
    //模仿mysql的hex函数
    public function hex($str){
            return (string)bin2hex($str);
    }
    //模仿mysql的unhex函数
    public function unhex($str){
            $arr = str_split($str,2);
            $val = '';
            foreach ($arr as $v) {
                $val .= hex2bin((int)$v);
            }
            return $val;
    }
}
