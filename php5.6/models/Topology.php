<?php

namespace App\Models;

class Topology extends Model
{
    protected $tableName = 'topology';
    protected $tablePrefix = '';
    protected $pk = 'topology_id';

    public $where = ['topology_status' => 1];
    public $field = '*';


    public function init()
    {

        if (!in_array($_SESSION['rid'], [1, 2, 3])) {

            $this->where['user_id'] = $_SESSION['uid'];
        }

    }


    public function queryAll($page = 1, $page_size = 20, $field = 'topology_id,topology_name,topology_remark,topology_remark,topology_img,update_time,create_time',$where = [])
    {

        if (!empty($field)) $this->field = $field;

        if (!empty($where)) $this->where = array_merge($this->where, $where);

        $rs['total_num'] = $this->field($this->field)->where($this->where)->count();

        $rs['total_page'] = strval(ceil($rs['total_num'] / $page_size));

        if ($rs['total_page'] < $page) jsonResult(['total_num' => $rs['total_num'], 'total_page' => $rs['total_num'], 'list' => []]);

        $rs['list'] = $this->field($this->field)->where($this->where)->page($page, $page_size)->select();
        //echo $this->getlastsql();die;
        return $rs;
    }

    public function queryOne($field = '*', $where = [])
    {

        if (!empty($field)) $this->field = $field;

        if (!empty($where)) $this->where = array_merge($this->where, $where);

        return $this->field($this->field)->where($this->where)->find();
    }
}