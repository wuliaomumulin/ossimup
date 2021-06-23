<?php

namespace App\Models;

class Systemconfig extends Model
{
    protected $tableName = 'system_config';
    protected $tablePrefix = '';
    protected $pk = 'id';

    protected $_auto = array(
        array('type', '2',1),
        array('status', '1',1),
    );

    /**
     * @abstract 查询满足条件的结果
     * @param string $field 查询字段
     * @param array $where 条件
     * @param string $group 分组
     * @param string $order 排序
     * @param $page 当前页
     * @param $page_size 每页数量
     * @param string $join 连表
     * @return mixed 结果集
     */
    public function getDataList($field = '*', $where = [], $group = '', $order = '', $page = 0, $page_size = 10, $join = '')
    {   
        $where['a.user_id'] = ['in',[0,$_SESSION['uid']]];
        $rs['total_num'] = strval($this->alias('a')->join($join)->where($where)->count());
        $rs['total_page'] = strval(ceil($rs['total_num'] / $page_size));
        $rs['page_size'] = strval($page_size);
        $rs['page'] = strval($page);

        if ($rs['total_num'] == 0) {
            $rs['list'] = [];
        } else {
            $rs['list'] = $this->field($field)->alias('a')->join($join)->where($where)->page($page, $page_size)->group($group)->order($order)->select();
        }

        return $rs;
    }

    public function getWhere($search = [])
    {
        if (empty($search)) return $search;

        foreach ($search as $k => $v) {

            if (strlen($v) > 0) {
                switch ($k) {
                    case 'name':
                        $rs['a.' . $k] = ['like', "{$v}"];
                        break;
                    case 'value':
                        $rs['a.' . $k] = ['like', "{$v}"];
                        break;
                    case 'desc':
                        $rs['a.' . $k] = ['like', "{$v}"];
                        break;
                    case 'status':
                        $rs['a.' . $k] = ['like', "{$v}"];
                        break;
                    default:
                        //continue;
                        break;
                }
            }
        }
        return $rs;
    }

    //判断当前用户属于角色
    protected function switchrole(){
        if(!in_array($_SESSION['rid'],$this->roles)){
            $this->role='guard';
            return TRUE;
        }else{
            $this->role='admin';
            return TRUE;
        }
    }

}