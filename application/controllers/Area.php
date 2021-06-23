<?php
use App\Models\Area;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class AreaController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
    protected $num = 14;

    public function init()
    {
        parent::init();

        $this->config = \Yaf\Registry::get("config");
        $this->model = new Area();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        $this->checkAuth($this->num);

    }

    //增加 修改
    public function addAreaAction()
    {
        $data = input('post.');
        $res = $this->model->addArea($data);
        if(intval($res) > 0){
            $this->logger(105);
            jsonResult([], '保存区域成功');
        }else{
            $this->logger(106);
            jsonError('保存区域失败');
        }
    }

    //子集区域的新增
    public function addSonAreaAction()
    {
        $data = input('post.');
        $res = $this->model->addSonArea($data);
        if(intval($res) > 0){
            $this->logger(105);
            jsonResult([], '保存区域成功');
        }else{
            $this->logger(106);
            jsonError('保存区域失败');
        }
    }


    //获取列表
    public function getAreaAction()
    {
        $data = $this->model->getArea();
        jsonResult($data);
    }

    //删除
    public function delAreaAction()
    {
        $id = input('post.id');
        $res = $this->model->delArea($id);
        if(intval($res) > 0){
            $this->logger(107);
            jsonResult([], '删除区域成功');
        }else{
            $this->logger(108);
            jsonError('删除区域失败');
        }
    }

}