<?php

use App\Models\Log;

/*
	用户操作日志
*/

class LogController extends Base
{
    protected $model = null;
    protected $num = 29;

    public function init()
    {
        parent::init();
        $this->model = new Log();
        $this->checkAuth($this->num);
    }

    public function indexAction()
    {
        $page = \Aes::decrypt(input('page', 0));
        $pagesize = \Aes::decrypt(input('pagesize', 10));
        $model = $this->model;
        $where = self::prev();
        $count = $model->where($where)->count();
        $datalist = $model->field('id,user_name,log_event,log_ip,remark,operation_time')->where($where)->order('id desc')->page($page, $pagesize)->select();
        $res = encryptcode(json_encode($datalist, 1));
        echo resultstatus(0, '数据列表', $res, $count);
    }

    /**
     * 前置操作
     */
    private function prev()
    {
        $id = \Aes::decrypt(input('post.id/d', 0));
        $log_event = \Aes::decrypt(input('post.log_event/s', ''));
        $log_ip = \Aes::decrypt(input('post.log_ip/s', ''));
        $user_name = \Aes::decrypt(input('post.user_name/s', ''));
        $begindate = \Aes::decrypt(input('post.begindate/s', ''));
        $enddate = \Aes::decrypt(input('post.enddate/s', ''));
        $where = [];

        if ($id <> 0) {
            $where['id'] = $id;
        }
        if (!empty($log_event)) {
            $where['log_event'] = $log_event;
        }
        if (!empty($user_name)) {
            $where['user_name'] = $user_name;
        }
        if (!empty($log_ip) && filter_var($log_ip, FILTER_VALIDATE_IP)) {
            $where['log_ip'] = $log_ip;
        }

        //时间范围
        if (!\Tools::isEmpty($begindate) and !\Tools::isEmpty($enddate)) {
            $where['operation_time'] = array(array('egt', $begindate), array('elt', $enddate));
        }

        return $where;

    }
}

?>