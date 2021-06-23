<?php
use App\Models\Log;
/*
	用户操作日志
*/
class LogController extends Base{
	protected $model=null;
	public function init(){
        parent::init();
		$this->model=new Log();
	}
	public function indexAction()
    {
       $page    = input('page',0);
       $pagesize= input('pagesize',10);
       $model = $this->model;
       $where = array();
       $count = $model->where($where)->count();
       $datalist = $model->field('id,user_name,log_event,log_ip,remark,performStartTime,operation_time,insert_time,update_time')->where($where)->order('id desc')->page($page, $pagesize)->select();
       echo resultstatus(0,'数据列表',$datalist,$count);
    }
}
?>