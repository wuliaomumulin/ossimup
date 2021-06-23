<?php
use App\Models\Config;

/* 
 * 运维相关
 */
class OperatorController extends Base{

	public function init()
    {

         
    }

	//关闭管理平台IP访问限制
	public function disablerequestipAction(){
		$config  = new Config();
		$config->disableRequestIp();
		jsonResult([]);
	}
}