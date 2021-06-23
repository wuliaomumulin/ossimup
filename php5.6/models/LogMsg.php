<?php
namespace App\Models;

class LogMsg extends Model
{
    protected $tableName = 'log_msg';
    protected $tablePrefix = 'sys_';
    protected $pk = 'id';

    //获取启动禁用状态
    public function getStatus($code)
    {
        return $this->alias('a')->field('b.status')->join('left join sys_log_type b on b.menu_id = a.log_type_id')->where(['a.id' => $code])->find();
    }

    public function getSysLogMsg($code)
    {
        return $this->alias('a')->field('a.msg,c.name')->join('left join sys_log_type b on b.menu_id = a.log_type_id left join yaf_menu c on c.id = b.menu_id')->where(['a.id' => $code])->find();
    }
    //获取模块名和操作信息
}