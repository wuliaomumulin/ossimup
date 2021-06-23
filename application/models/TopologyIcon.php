<?php

namespace App\Models;

class TopologyIcon extends Model
{
    protected $tableName = 'icon';
    protected $tablePrefix = 'topology_';
    protected $pk = 'id';


    public function getAll($where=[]){
        return $this->where($where)->select();
    }

    
}