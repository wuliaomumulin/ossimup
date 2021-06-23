<?php
use App\Models\NetHost;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class NethostController extends Base
{
    protected $conf = './list_conf/netAssetManager.json';
    protected $num = 54;

    public function init()
    {
        parent::init();

        $this->config = \Yaf\Registry::get("config");
        $this->uid = $_SESSION['uid'];
        $this->model = new NetHost();
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        $this->checkAuth($this->num);

    }


    //修改展示的list
    public function updateListConfigAction()
    {
        $str = input('post.name');
        if (!\Tools::isEmpty($str)) {
            $data = explode(',', $str);
            $config = json_decode(file_get_contents($this->conf), 256);
            foreach ($config as $key => &$val) {
                if (in_array($val['name'], $data) === false) {
                    $val['show'] = false;
                } else {
                    $val['show'] = true;
                }
            }
            if (file_put_contents($this->conf, json_encode($config, 256)) !== false) {
                $this->logger(99);
                jsonResult(['status' => true]);
            } else {
                $this->logger(100);
                jsonResult(['status' => false]);
            }
        }
    }

    public function addNetHostAction()
    {
        $data = input('post.');
        //验证ip段
        $Network = new Network();
        $status =  $Network->vaildation_network(decryptcode($data['ips']));
        if($status !== true){
            jsonError($status);
        }

        //验证是否重复
//        $is_have = $this->model->isHave($data['ips']);
//        if(!empty($is_have)){
//            jsonError('ip已存在！');
//        }


        $res = $this->model->addNetHost($data);
        if($res === true){
            $this->logger(95);
            jsonResult([], '保存网络资产成功');
        }elseif($res === 0){
            $this->logger(96);
            jsonError('IP已存在!');
        }elseif($res === false){
            $this->logger(96);
            jsonError('保存网络资产失败');
        }
    }

    public function delNetHostAction()
    {
        $id = input('post.id');
        $res = $this->model->delNetHost($id);
        if($res === true){
            $this->logger(97);
            jsonResult([], '删除网络资产成功');
        }else{
            $this->logger(98);
            jsonError('删除网络资产失败');
        }
    }

    public function getOneNetHostAction()
    {
        $id = input('post.id');
        if(!empty($id)){
            $res = $this->model->getOneNetHost($id);
            if(!empty($res)){
                jsonResult($res);
            }else{
                jsonError('获取信息失败');
            }
        }else{
            jsonError('参数错误');
        }

    }

    public function getNetHostListAction()
    {
        $page = input('page', 1);
        $pagesize = input('page_size', 10);
        $where = input('post.');
        $data = $this->model->getNetHostList($where, $page, $pagesize);
        jsonResult($data);
    }

}
?>