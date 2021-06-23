<?php

use App\Models\Topology;
use App\Models\Import;
use App\Models\Host;
use App\Models\DeviceTypes;
use App\Models\HostTypes;
use App\Models\Es;
use App\Models\Factory;

/**
 * -------------------------------------------------
 * @abstract  事件列表控制器
 * -------------------------------------------------
 * @author   wangxiaohui
 * -------------------------------------------------
 */
class TopologyController extends Base
{

    protected $model = null;
    protected $num = 15;

    public function init()
    {
        parent::init();
        $this->config = \Yaf\Registry::get("config");
        $this->model = new Topology();

        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        $this->model->where['user_id'] = $this->uid;
        $this->checkAuth($this->num);
    }

    /**
     * @abstract 获取拓扑图列表
     * @return [type] [description]
     */
    public function getTopologyListAction()
    {

        $page = input('post.page/d', 1);
        $page_size = input('post.page_size/d', 20);
        $topology_name = input('post.topology_name', '');

        //xss过滤事件
        $topology_name = $this->model->xss_filter($topology_name);
        //var_dump($topology_name);die;
        if (!empty($topology_name)) $this->model->where['topology_name'] = ['like', '%' . $topology_name . '%'];

        jsonResult($this->model->queryAll($page, $page_size));
    }

    /**
     * @abstract 保存/更新 拓扑图
     * @return [type] [description]
     */
    public function saveTopologyAction()
    {

        $data['topology_id'] = input('post.topology_id', 0, 'int');

        $data['topology_name'] = input('post.name');
        $data['topology_remark'] = input('post.remark');
        $data['user_id'] = $this->uid;

        $data['topology_content'] = $this->upload();
        $data['update_time'] = date('Y-m-d H:i:s');

        if (!$data['topology_content']) jsonError('无效的参数');

        if (!empty($data['topology_id'])) {

            $user_id = $this->model->queryOne('user_id', ['topology_id' => $data['topology_id']])['user_id'];

            if ($user_id != $this->uid) jsonError('您无权修改此拓扑图或此拓扑图已被删除');

            $this->model->where(['topology_id' => $data['topology_id']])->save($data);

            $rs = $data['topology_id'];

        } else {
            unset($data['topology_id']);
            $rs = $this->model->add($data);
            $this->logger(8);
        }

        jsonResult($rs);
    }

    public function saveTopologyImgAction()
    {

        $data['topology_id'] = input('post.topology_id', 0, 'int');

        if (empty($data['topology_id'])) jsonError('无效的参数');

        $data['topology_img'] = $this->upload('jpg|png', 'topologty/image/');

        $rs = $this->model->where(['topology_id' => $data['topology_id']])->save($data);

        jsonResult($rs);
    }

    /**
     * @abstract 删除拓扑图
     * @return [type] [description]
     */
    public function deleteTopologyAction()
    {

        $data['topology_id'] = input('get.topology_id', 0, 'int');

        if (empty($data['topology_id'])) jsonError('无效的参数');

        $info = $this->model->queryOne('user_id,topology_content,topology_img', ['topology_id' => $data['topology_id']]);

        if ($info['user_id'] != $this->uid){

            $this->logger(12);
            jsonError('您无权删除此拓扑图或此拓扑图已被删除');
        }


        $data['topology_status'] = -1;
        $this->model->save($data);

        if (file_exists($info['topology_content'])) {

            unlink($info['topology_content']);
        }
        if (file_exists($info['topology_img'])) {
            unlink($info['topology_img']);
        }

        $this->logger(11);
        jsonResult();
    }

    public function getTopologyIconAction()
    {

        $data = $this->model->query("SELECT * FROM topology_icon WHERE `group` = '网络资产' and `status` = 1");

        if (!empty($data)) {

            $rs = [];

            foreach ($data as $k => $v) {
                $rs[$v['group']]['group'] = $v['group'];
                $rs[$v['group']]['children'][] = [
                    'name' => $v['title'],
                    'icon' => $v['icon'],
                    'data' => [
                        'text' => $v['title'],
                        'rect' => [
                            'width' => intval($v['width']),
                            'height' => intval($v['height'])
                        ],
                        'strokeStyle' => $v['strokeStyle'],
                        'name' => $v['name'],
                        'image' => $v['icon']
                    ]
                ];
            }
            jsonResult(array_values($rs));
        }

        jsonResult($data);

    }

    /**
     * @abstract 获取拓扑图详情
     */
    public function getTopologyInfoAction()
    {

        $data['topology_id'] = input('get.topology_id', 0, 'int');

        if (empty($data['topology_id'])) jsonError('无效的参数');

        $info = $this->model->queryOne('user_id,topology_content', ['topology_id' => $data['topology_id']]);

        if ($info['user_id'] != $this->uid) jsonError('您无权查看此拓扑图');


        if (!file_exists($info['topology_content'])) jsonError('文件不存在或者已经损坏');

        ob_end_clean();

        ob_start();

        $handler = fopen($info['topology_content'], 'r+b');
        $file_size = filesize($info['topology_content']);

        header("Content-type: application/octet-stream");
        header("Accept-Rangs: bytes");
        header("Accept-Length: " . $file_size);
        header("Content-Disposition: attachment; filename=" . basename($info['topology_content']));

        echo fread($handler, $file_size);

        fclose($handler);

        ob_end_flush();

        exit();
    }

    /**
     * 上传文件
     */
    public function upload($type = 'xls|xlsx|csv|xml', $url = 'topologty/xml/')
    {

        set_time_limit(300);
        $upload = new spUploadFile();

        if (!Tools::isEmpty($_FILES['file'])) {

            $path = APP_PATH.'/html/upload/';

            $url = 'topologty/xml/' . date('Y-m-d') . '/';

            if (!file_exists($path . $url)) {
                mkdir($path . $url, '0777');
                chmod($path . $url, 0777);
            }

            $url = $upload->upload_file($_FILES['file'], $type, $url);

            if ($upload->errmsg == '') {

                $Import = new Import();

                $Import->index($upload->uploaded, 'check');

                return $upload->uploaded;

                $this->logger(9);
                jsonResult(
                    [
                        'data' => $Import->error,
                        'file' => $upload->uploaded,
                        'total_num' => $Import->allRows,
                    ]
                    , "上传文件成功");

            } else {
                $this->logger(10);
                jsonError($upload->errmsg);
            }
        } else {
            return false;
        }
    }

    /**
     * 解析文件
     */
    public function importAction()
    {
        set_time_limit(300);
        $file = input('file', '');
        if ($file <> '' && file_exists($file)) {
            $Import = new Import();
            $Import->index($file, 'save');
            jsonResult(
                [
                    $Import->error
                ]
                , "导入文件成功");
        } else {
            jsonError('无文件路径或文件不存在');
        }
    }


    /**
    * 根据英文分类获取资产
    */
    public function gethostAction(){
        $enname = input('post.enname/s','Network Device');
        
        if(\Tools::isEmpty($enname)){
            jsonError('enname参数不正确');
        }

        $DeviceTypes = new DeviceTypes();
        $HostTypes = new HostTypes();
        $host = new Host();

        $result = array();

        $type = $DeviceTypes->getId(['enname'=>$enname]);
        if(!is_null($type)){
            $ids = $HostTypes->getHostId(['type'=>$type]);

            if(!is_null($ids)){


                $idstr = '';
                foreach($ids as $v){
                    $idstr .= "unhex('{$v}'),";
                }
                $idstr = rtrim($idstr,',');
                $where = array('id'=>array('exp',"in({$idstr})"));
                $data = $host->field('hex(id) id,hostname')->where($where)->select();


            }


        }

        if(!is_null($data)){
            /**  构造骨架单元 begin*/
            $item = [
                'name' => 0,
                'icon' => "upload/topologty/icon/shebei.png",
                'data' => [
                    "text" => 0,
                    "rect" => [
                        'width' => 50,
                        'height' => 50,
                    ],
                    "strokeStyle"=>"#ffffff00",
                    "name"=>"rectangle",
                    "image"=>"upload/topologty/icon/shebei.png"
                ]
            ];

            switch ($enname) {
                case 'Network Device':
                    $item['icon'] = "upload/topologty/icon/wangguan.png";
                    $item['data']['image'] = "upload/topologty/icon/caijiqi.png";
                    break;
                case 'Server':
                    $item['icon'] = "upload/topologty/icon/fuwuqi.png";
                    $item['data']['image'] = "upload/topologty/icon/fuwuqi.png";
                    break;
                case 'Acquisition Device':
                    $item['icon'] = "upload/topologty/icon/caijiqi.png";
                    $item['data']['image'] = "upload/topologty/icon/caijiqi.png";
                    break;              
                default:
                    # code...
                    break;
            }

            /** 构造骨架单元 end */
            /* 构建数据集 */
            $result = array_map(function($v) use(&$item){
                $item['name'] = $v['hostname'];
                $item['data']['text'] = $v['hostname'];
                return $item;
            },$data);

        }


        jsonResult($result);




    } 

}