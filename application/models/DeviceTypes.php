<?php
namespace App\Models;

class DeviceTypes extends Model{
    protected $tableName = 'types';
    protected $tablePrefix = 'device_';
    protected $pk = 'id';

    /**
	* 获得分类列表树
    */
 	public function tree(){
 		$treedata = $this->select();
 		$treeList = \Tree::instance()->formatTree($treedata,0,'class');
 		return $treeList;
 	}
 	/**
    * 获取属性
    */
    public function getId($where = []): ?string
    {
        $result = $this->where($where)->getField('id');        
        return $result;
    }
    /**
    * 获取属性
    */
    public function getName($where = []): ?string
    {
        $result = $this->where($where)->getField('name');        
        return $result;
    }
    /***
    * 获得一级分类列表
    */
    public function type1(){
        return $this->field('id,name,enname')->where(['class'=>0])->select();
    }    
}