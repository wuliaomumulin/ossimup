<?php

use App\Models\LogType;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class LogtypeController extends Base
{
    protected $logType;


    public function init()
    {
        parent::init();
        $this->logType = new LogType();
    }

    //获取日志配置列表
    public function getLogTypeAction()
    {
        $page_size = input('get.page_size', 10);
        $page = input('get.page', 1);
        $data['total_num'] = $this->logType->getCount();
        $data['total_page'] = ceil($data['total_num'] / $page_size);
        $data['list'] = $this->logType->getLogType($page,$page_size);
        jsonResult($data);
    }

    //启用禁用
    public function logStatusUpdAction()
    {
        $data = input('post.');
        if (!\Tools::isEmpty($data)) {
            $data['id'] = decryptcode(input('post.id'));
            $data['status'] = decryptcode(input('post.status'));
            $result = $this->logType->logStatusUpd($data);
            if ($result == 0) {
                $this->logger(50);
                jsonError('保存失败');
            }
            $this->logger(49);
            jsonResult(['res' => '保存成功']);
        }
    }
}