<?php

namespace App\Models;

class Area extends Model
{
    protected $tableName = 'area';
    protected $tablePrefix = '';
    protected $pk = 'id';

    public function addArea($params)
    {
        if (!isset($params['id'])) {
            $params['pid'] = 0;
            $res = $this->add($params);
        } else {
            $res = $this->save($params);
        }
        return $res;
    }

    public function addSonArea($params)
    {
        $data['name'] = $params['name'];
        $data['pid'] = $params['id'];
        $res = $this->add($data);
        return $res;
    }


    public function getArea()
    {
        $data = $this->select();
        $treeList = \Tree::instance()->formatTree($data, 0, 'pid');
        return $treeList;
    }

    public function delArea($id)
    {
        $data = $this->field('id')->where("pid={$id}")->select();
        if (!empty($data)) {
            $ids = [];
            foreach ($data as $k => $v) {
                array_push($ids, $v['id']);
            }
            array_push($ids, $id);
            $str = implode(',', $ids);
            $res = $this->delete($str);
        } else {
            $res = $this->where("id={$id}")->delete();
        }

        return $res;
    }
}
