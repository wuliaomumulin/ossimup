<?php

namespace App\Models;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class CustomVerify extends Model
{
    protected $tableName = 'verify';
    protected $tablePrefix = 'custom_';
    protected $pk = '';

    protected $server = 'localhost';

//    public  function __construct($where)
//    {
//        $this->where = self::getWhere($where);
//    }

    /**
	* 获得当前平台配置
    */
    public function getInfo()
    {
    	//记录一下，调出实施配置的信息，然后再拼接到事件列表中
    	$where = [
    		'attribute' => ['in',[
    			'factory',
    			'factory_type',
    			'company',
    			'contact',
    			'equ_num',
    			'sim_num',
    			'channel_num',
    			'nick_name',
    			'memo',
    			'address',
    			'factory_person',
    			'factory_phone',
    			'telphone',
    			'province',
    			'city',
    			'county',
    			'lat',
    			'lng',
    			'isp',
    			'accept_time',
    		]]
    	];
    	$result = $this->field('attribute,value')->where($where)->select();
    	return array_combine(array_column($result,'attribute'),array_column($result,'value'));
    	# code...
    }

    /*
     * 系统信息  ip
     * */
    public function getServiceIp()
    {
        return $this->serverLink($this->server, 'get_system_sensor_info');
    }
    /*
     * 访问服务
     * */
    private function serverLink($server, $type, $param = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://' . $server . ':8080/interface/edit?type=' . $type . $param);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);

        curl_close($curl);
        return json_decode($data, 255);
    }    
}