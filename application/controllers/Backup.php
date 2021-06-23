<?php

use App\Models\BackUp;
use App\Models\Avbackup;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class BackupController extends Base
{
    protected $backUp;
    protected $num = 25;

    public function init()
    {
        parent::init();
        $this->backUp = new Backup();
        $this->checkAuth($this->num);
    }

    //获取安全日志现有的
    public function getNowHaveLogAction()
    {
        set_time_limit(300);
        $redis = new \phpredis();
        if ($redis->get('event-have')) {
            $data = json_decode($redis->get('event-have'), 1);
        }else{
            $data = $this->backUp->getNowHaveLog();
            $redis->set('event-have', json_encode($data, 256), 0, 0, 600);
        }

        jsonResult($data);
    }

    //安全日志可还原的
    public function getRestoreAction()
    {
        set_time_limit(300);
        $redis = new \phpredis();
        if ($redis->get('event-huanyuan')) {
            $data = json_decode($redis->get('event-huanyuan'), 1);
        }else{
            $data = $this->backUp->getRestore();
            $redis->set('event-huanyuan', json_encode($data, 256), 0, 0, 600);
        }

        jsonResult($data);
    }

    //安全日志记录
    public function getBackListAction()
    {
        $page = input('get.page/d',1);
        $page_size = input('get.page_size/d',10);
        $data['total_num'] = $this->backUp->getBackListCount();
        $data['total_page'] = ceil($data['total_num'] / $page_size);
        $data['list'] = $this->backUp->getBackList($page,$page_size);
        jsonResult($data);
    }

    //安全日志还原
    public function restoreAction()
    {
        $dates_list = input('post.dates_list/a', '');
        if(!empty($dates_list)){
            $data = $this->backUp->restore($dates_list);
            if($data['status'] == 'success'){
                $this->logger(34,$data['info']);
            }else{
                $this->logger(35,$data['info']);
            }
            jsonResult($data);
        }else{
            jsonError('请选择可还原的文件');
        }

    }

    //安全日志状态
    public function statusAction()
    {
        $data = $this->backUp->status();
        jsonResult($data);
    }

    //安全日志清除(清除数据库)
    public function deleteAction()
    {
        //$dates_list = input('post.dates_list/a', '');
        $data = $this->backUp->delete();
        if($data['status'] == 1){
            $this->logger(34,$data['info']);
        }else{
            $this->logger(35,$data['info']);
        }
        jsonResult($data);
    }

    public function aAction()
    {
        $this->backUp->setFinished();
    }

    //获取审计日志现有的
    public function getNowAuditLogAction()
    {
        set_time_limit(300);
        $redis = new \phpredis();
        if ($redis->get('audit-huanyuan')) {
            $data = json_decode($redis->get('audit-huanyuan'), 1);
        }else{
            $data = $this->backUp->getNowAuditLog();
            $redis->set('audit-huanyuan', json_encode($data, 256), 0, 0, 600);
        }

        jsonResult($data);
    }

    //获取审计日志备份记录
    public function getAuditListAction()
    {
        $page = input('get.page/d',1);
        $page_size = input('get.page_size/d',10);
        $data['total_num'] = $this->backUp->getAuditListCount();
        $data['total_page'] = ceil($data['total_num'] / $page_size);
        $data['list'] = $this->backUp->getAuditList($page,$page_size);
        jsonResult($data);
    }

    //获取审计日志备份现存库里的记录
    public function getNowHaveAuditAction()
    {
        set_time_limit(300);
        $redis = new \phpredis();
        if ($redis->get('audit-have')) {
            $data = json_decode($redis->get('audit-have'), 1);
        }else{
            $data = $this->backUp->getNowHaveAudit();
            $redis->set('audit-have', json_encode($data, 256), 0, 0, 600);
        }

        jsonResult($data);
    }


    //审计日志恢复
    public function restoreAuditLogAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            $data = $this->backUp->restoreAuditLog(input('post.'));
            if( $data['status']!= 'failed'){
                $this->logger(34,$data['info']);
            }else{
                $this->logger(35,$data['info']);
            }
            jsonResult($data);
        }else{
            jsonError('请选择可还原的文件');
        }
    }

    //审计日志删除数据库操作
    public function deleteAuditAction()
    {
        $data = $this->backUp->deleteAudit();
        if( $data['status'] == 1){
            $this->logger(34,$data['info']);
        }else{
            $this->logger(35,$data['info']);
        }
        jsonResult($data);
    }

}

?>
