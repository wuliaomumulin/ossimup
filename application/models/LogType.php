<?php
namespace App\Models;

class LogType extends Model
{
    protected $tableName = 'log_type';
    protected $tablePrefix = 'sys_';
    protected $pk = 'id';

    public function getLogType($page,$page_size)
    {
        return $this->alias('a')->field('a.id,a.status,b.name')->join('left join yaf_menu b on a.menu_id = b.id')->page($page, $page_size)->select();
    }

    public function getCount()
    {
        return $this->alias('a')->count('id');
    }

    public function logStatusUpd($param)
    {
       return $this->where(['id' => $param['id']])->save(['status' => $param['status']]);
    }

}