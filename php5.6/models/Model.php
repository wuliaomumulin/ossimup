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

}
