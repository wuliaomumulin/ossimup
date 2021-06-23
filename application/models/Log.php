<?php
namespace App\Models;

class Log extends Model
{
    protected $tableName = 'log';
    protected $tablePrefix = 'sys_';
    protected $pk = 'id';
    
    protected $_validate = array(
            array('user_name','require','用户名称不能为空'),
            array('log_event','require','用户事件不能为空'),
            array('log_ip','require','用户IP不能为空'),
        );
    //自动完成
    // protected $_auto = array (
    //         array('performStartTime','getdatatime',1,'callback'),
    //         array('operation_time','getdatatime',1,'callback'),
    //         array('insert_time','getdatatime',1,'callback'),
    //         array('update_time','getdatatime',3,'callback'),
    //     );

    //日志埋点
    public function syslogadd($data){

        if (!$this->create($data)){
            $errtips = $this->getError();
            jsonError($errtips);             
        }else{
           return $this->add();

        }
    }


}
