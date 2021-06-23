<?php

use App\Models\Config;

class ChartsController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
 
    public function init()
    {
        parent::init();
        $this->config = \Yaf\Registry::get("config");
        $this->model = new Config();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
         
    }

    /**
    * 系统状态图表
    **/
    public function systemstatusAction()
    {      
        jsonResult($this->model->system_status_chart());

    }
}