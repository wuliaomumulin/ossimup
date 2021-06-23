<?php

use App\Models\Monitor;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
/*
 *	监控项---检测审计
 */

class MonitorController extends Base
{
    protected $model = null;
    protected $num = 44;

    public function init()
    {
        parent::init();
        $this->model = new Monitor();
        $this->checkAuth($this->num);
    }

    public function querylistAction()
    {
        $page = input('page/d', 0);
        $pagesize = input('pagesize/d', 10);
        $datalist = $this->model->allAgent($page, $pagesize);
        jsonResult($datalist);
    }

    /*
    * 网络白名单
    */
    public function WhiteListAction()
    {
        $where['ip'] = input('ip/s', 0);//
        $where['port'] = input('port/d', 8801);//
        $where['para'] = input('para/d', 16);//

        //xss过滤事件
        $where['ip'] = $this->model->xss_filter($where['ip']);

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }

        $ret = $this->model->WhiteList($where);
        jsonResult($ret);
    }

    /**
     * 检测审计--白名单-保存
     */
    public function saveWhiteAction()
    {
        $where['ip'] = input('post.ip/s', 0);//
        $where['port'] = input('post.port/d', 8801);//
        $where['para'] = input('para/d', 16);//
        $where['id'] = input('post.id/d', 0);//
        $where['item'] = input('post.item/s', '');//

        $Network = new Network();
        $status = $Network->vaildation_network($where['item']);

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:ip');
        }

        if ($status !== true) {
            jsonError($status);
        }

        $ret = $this->model->saveWhite($where);
        if ($ret) {
            jsonResult('操作成功');
        } else {
            jsonError($this->model::$errors);
        }

    }

    /**
     * 检测审计--白名单-删除
     */
    public function destroyWhiteAction()
    {
        $where['ip'] = input('ip/s', 0);//
        $where['port'] = input('port/d', 8801);//
        $where['para'] = input('para/d', 16);//
        $where['id'] = input('post.id/d', 0);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }
        if ($where['id'] == 0) {
            jsonError('无效的参数:id');
        }
        $ret = $this->model->destroyWhite($where);
        if ($ret) {
            jsonResult('操作成功');
        } else {
            jsonError($this->model::$errors);
        }

    }

    /*
    * 协议
    */
    public function protocolListAction()
    {
        $where['ip'] = input('ip/s', 0);//
        $where['port'] = input('port/d', 8801);//
        $where['para'] = input('para/d', 17);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:资产ip');
        }

        $ret = $this->model->ProtocolList($where);
        jsonResult($ret);
    }

    /**
     * 检测审计--协议-保存
     */
    public function saveProtocolAction()
    {
        $where['ip'] = input('post.ip/s', 0);//
        $where['port'] = input('post.port/d', 8801);//
        $where['para'] = input('para/d', 17);//
        $where['id'] = input('post.id/d', 0);//
        $where['protocol'] = input('post.protocol/s', '');//
        $where['status'] = input('post.status/s', '');//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:ip');
        }
        if ($where['id'] == 0) {
            jsonError('无效的参数:id');

        }

        $ret = $this->model->saveProtocol($where);
        if ($ret) {
            jsonResult('操作成功');
        } else {
            jsonError($this->model::$errors);
        }

    }

    /**
     * 检测审计--资产扫描
     */
    public function scanAction()
    {
        $where['ip'] = input('post.ip/s', 0);//
        $where['port'] = input('post.port/d', 8801);//

        if (Tools::isEmpty($where['ip']) OR !filter_var($where['ip'], FILTER_VALIDATE_IP)) {
            jsonError('无效的参数:ip');
        }

        $ret = $this->model->scan($where);
        if ($ret) {
            jsonResult('操作成功');
        } else {
            jsonError($this->model::$errors);
        }

    }
}