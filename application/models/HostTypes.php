<?php

namespace App\Models;

class HostTypes extends Model{
    protected $dbName = 'alienvault';
    protected $tableName = 'types';
    protected $tablePrefix = 'host_';
    protected $pk = 'host_id';


    /**
	* 各种类资产数量
    */
    public function typeCount(){

    	$field = 'count(1) `value`,b.name bname,c.name cname';
    	$result = $this->field($field)->alias('a')->join('device_types b on a.type=b.id','INNER')->join('device_types c on a.subtype=c.id','INNER')->join('`host` d on a.host_id=d.id','INNER')->group('b.id,c.id')->select();
    	//格式化字符
    	array_walk($result,function(&$arr){
    		$arr['name'] = is_null($arr['cname']) ?  $arr['bname'] :  $arr['cname'];
    		unset($arr['bname'],$arr['cname']);
    	});

    	return $result;
    }
    /**
    * 获取属性
    */
    public function getHostId($where = []): ?array
    {
        $result = $this->where($where)->field('hex(host_id) id')->select();
        return array_column($result, 'id');
    }

    //获取资产id
    public function getHostType($param)
    {
        $result = $this->table('device_types')->where(['name' => $param])->getField('id');
        return $result;
    }

    //根据探针资产类型获取探针资产IP
    public function getProbeAssetIp($ip){

        $ip = $this->query("SELECT inet6_ntoa ( b.ip ) ip from host_types a INNER JOIN host_ip b on a.host_id = b.host_id INNER JOIN host d on d.id = a.host_id WHERE 1 AND a.subtype in({$ip}) GROUP BY b.ip");
      
       return $ip;
    }

}