<?php
namespace App\Models;

class Config extends Model{
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';

    /**
	* 系统状态链接
     */
    public function system_status_chart(){
    	$result = $this->where(['conf'=>'system_status_chart'])->getField('`value`');
    	return (!\Tools::isEmpty($result)) ? ($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$result) : false;
    }
    /**
    * 获取业务网口
    */
    public function getEth(){
        $result = $this->where(['conf'=>'frameworkd_eth'])->getField('`value`');
        return (!\Tools::isEmpty($result)) ? $result : false;
    }

    //网络拓扑
    public function networktopology()
    {
        $result = $this->where(['conf'=>'network_topology_chart'])->getField('`value`');
       // return (!\Tools::isEmpty($result)) ? ($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].':'.$result) : false;
        return (!\Tools::isEmpty($result)) ? ('http://'.$_SERVER['SERVER_ADDR'].':'.$result) : false;
    }

    public function hostevent()
    {
        $result = $this->where(['conf'=>'host_event_chart'])->getField('`value`');
        return (!\Tools::isEmpty($result)) ? ($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$result) : false;
    }
    public function tclevent()
    {
        $result = $this->where(['conf'=>'tclevent_chart'])->getField('`value`');
        return (!\Tools::isEmpty($result)) ? ($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$result) : false;
    }
    //关闭管理平台IP访问限制
    public function disableRequestIp()
    {
        return $this->where(['conf'=>'enable_request_ip'])->setField('value',0);
    }
}