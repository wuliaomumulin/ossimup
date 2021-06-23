<?php

use App\Models\Plugin;
use App\Models\Log;
use App\Models\Userreference;
use App\Models\User;
use App\Models\Configsystem;
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
class PluginController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
    protected $num = 21;

    public function init()
    {
        parent::init();

        $this->config = \Yaf\Registry::get("config");
        $this->model = new Plugin();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        $this->checkAuth($this->num);

    }

    public function querylistAction(): void
    {
        $page = input('page/d', 1);
        $pagesize = input('page_size/d', 10);
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

        //xss过滤事件
        foreach($request as $k => $v) $request[$k] = $this->model->xss_filter($v);


        unset($request['page'], $request['page_size'], $request['begindate'], $request['enddate']);
        return $request;
    }

    /*
     * 插件添加、修改
     * */
    public function saveDataAction()
    {

        $edit = decryptcode(input("post.edit"));
        $id = input("post.id")?decryptcode(input("post.id")):0;
        !$id && jsonError('无效的参数');
        //当edit  == 1  为编辑插件 不需要验证ID的重复性

        if ($edit == 0) {

            $check = $this->model->field('id')->where("id = " . $id)->find();
            if ($check['id']) jsonError('重复的插件ID');

        }

        if (!\Tools::isEmpty($_POST)) {
            $data = $this->model->saveData($_POST);
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

    //安全库升级
    public function upgradeAction()
    {
        set_time_limit(300);
        $upload = new spUploadFile("/work/web/html/bdapi/upgrade",'upgrade');
        //var_dump($_FILES);die;
        if(!\Tools::isEmpty($_FILES['file'])){

            $url = $upload->upload_file($_FILES['file'],"zip");
            //  var_dump($url);die;
//            $url = getcwd().ltrim($url,'.');
            if($upload->errmsg==''){
                $Configsystem = new Configsystem();
                $data['res'] = $Configsystem->upgrade($url);
                unlink("/work/web/html/bdapi/upgrade/upgrade.zip");
                $this->logger(53);
                jsonResult($data);
            }else{
                $this->logger(54);
                jsonError($upload->errmsg);
            }
        }else{

            jsonError('无上传文件');
        }

    }



}