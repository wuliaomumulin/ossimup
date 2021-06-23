<?php

use App\Models\Plugin;
use App\Models\Log;
use App\Models\Userreference;
use App\Models\User;

class PluginController extends Base
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
        $this->model = new Plugin();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色

    }

    public function querylistAction()
    {

        $page = input('page', 1);
        $pagesize = input('page_size', 10);
        //$keywords			 = input('keywords','');//插件名称
        $keywords = self::prev();
        $datalist['config'] = json_decode(file_get_contents('./list_conf/threatPlugins.json'), 1);


        $where = array();

        if (!empty($keywords)) {

            foreach ($datalist['config'] as $k => $v) {

                if (strlen($keywords[$v['name']]) > 0) {

                    if ($v['type'] == 'input') {
                        if ($v['name'] == 'product_type_sname') {
                            $where['d.name'] = ['like', '%' . $keywords[$v['name']] . '%'];
                        } else {
                            $where['a.' . $v['name']] = ['like', '%' . $keywords[$v['name']] . '%'];
                        }

                    } elseif ($v['type'] == 'select') {

                        $where['a.' . $v['name']] = ['eq', $keywords[$v['name']]];
                    }
                }
            }
        }
        $da = $this->model->getList($where, $page, $pagesize);


        jsonResult(array_merge($datalist,$da));
    }

    /***
     * 前置方法，取出查询的唯一字段
     */
    public function prev()
    {
        $request = input('post.');
        unset($request['page'], $request['page_size'], $request['begindate'], $request['enddate']);
        return $request;
    }

    /*
     * 插件添加、修改
     * */
    public function saveDataAction()
    {

        $edit = input("post.edit");
        $id = input("post.id", 0, 'int');
        !$id && jsonError('无效的参数');
        //当edit  == 1  为编辑插件 不需要验证ID的重复性

        if ($edit == 0) {

            $check = $this->model->field('id')->where("id = " . $id)->find();
            if ($check['id']) jsonError('重复的插件ID');

        }

        if (!\Tools::isEmpty(input('post.'))) {
            $data = $this->model->saveData(input('post.'));
            if ($data == 1) {
                $this->logger(25);
            } else {
                $this->logger(26);
            }
            jsonResult(['status' => $data]);
        }
    }


    //获取插件类型
    public function getPluginTypeAction()
    {
        jsonResult($this->model->getPluginType());
    }

    //删除插件

    public function delPluginAction()
    {
        if (!\Tools::isEmpty(input('get.id'))) {
            $data = $this->model->delPlugins(input('get.id'));
            if ($data == 1) {
                $this->logger(27);
            } else {
                $this->logger(28);
            }
            jsonResult(['status' => $data]);
        }

    }


}