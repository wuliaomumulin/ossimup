<?php
namespace App\Models;
/**
* 安全事件和采集设备关联表
*/
class Device extends Model{
    // 数据库名称
    protected $dbName = 'alienvault_siem';
    protected $tableName = 'device';
    protected $tablePrefix = '';
    protected $pk = 'id';

    protected $event = '';


    // 回调方法 初始化模型
    protected function _initialize() {

        $this->event = new Event();

    }

    /**
	* 根据告警事件追溯报警设备
    */
 	public function eventIdToHostname($event_id = ''){
        $hash = 'table-ip-hostname';
 		$device_id = $this->event->where(['id'=>['EXP',"= unhex('{$event_id}')"]])->getField('device_id');
        $ip = $this->where(['id'=>$device_id])->getField('inet6_ntoa(device_ip) device_ip');
        if(is_null($ip)) return '未设置';
        $hostname  = $this->redis->hashGet($hash,$ip);
        return ($hostname == false) ? ('未命名IP'.$ip) : $hostname;
 	}
 
}