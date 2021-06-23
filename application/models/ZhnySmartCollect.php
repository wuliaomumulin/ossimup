<?php
namespace App\Models;

class ZhnySmartCollect extends Model{
    protected $tableName = 'collect';
    protected $tablePrefix = 'zhny_smart_';
    protected $pk = 'id';


    public function getAll($where = []){
    	return $this->where($where)->select();
    }


}