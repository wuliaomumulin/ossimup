<?php
namespace App\Models;

class Mechinegrouptype extends Model
{
    protected $tableName = 'type';
    protected $tablePrefix = 'zhny_group_';
    protected $pk = 'id';

    /**
	* 
    */
    public function getAll($where = []){
    	return $this->where($where)->select();
    }

}
