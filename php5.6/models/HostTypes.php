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
    	$result = $this->field($field)->alias('a')->join('device_types b on a.type=b.id','LEFT')->join('device_types c on a.subtype=c.id','LEFT')->group('b.id,c.id')->select();
        
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
    public function getHostId($where = [])
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

}