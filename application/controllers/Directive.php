<?php

use App\Models\Directive;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
class DirectiveController extends Base
{
    protected $directive;
    protected $num = 20;

    public function init()
    {
        parent::init();
        $this->directive = new Directive();
        $this->checkAuth($this->num);
    }

    //查
    public function getdatalistAction()
    {
        $datas = $this->directive->getDataList();
        jsonResult($datas);
    }

    //大规则的新增和编辑
    public function directiveAddAction()
    {
        $data['sid'] = input('post.sid/s', '');
        $data['attr'] = input('post.attr/a');
        $data['file_name'] = input('post.file_name/s');
        $data['kingdom'] = input('post.kingdom');
        $data['category'] = input('post.category');
        $data['subcategory'] = input('post.subcategory/s');
        $param = json_encode($data, 255);
        $res = $this->directive->directiveAdd($param);

        if ($res['status'] == '添加成功' || $res['status'] === '修改成功') {
            $this->logger(90);
            jsonResult();
        } else {
            $this->logger(91);
            jsonError();
        }

    }

    //删
    public function directiveDelAction()
    {
        $data['sid'] = input('get.sid/s');
        $data['file_name'] = input('get.file_name/s');

        $param = json_encode($data, 255);
        $res = $this->directive->directiveDel($param);
        if ($res['status'] == '删除成功') {
            $this->logger(92);
            jsonResult();
        } else {
            $this->logger(93);
            jsonError();
        }
    }

    //大规则下新增和编辑
    public function directiveUpdAction()
    {
        $data['sid'] = input('post.sid/s');
        $data['attr'] = input('post.attr/a');
        $data['file_name'] = input('post.file_name/s');
        $data['edit'] = input('post.edit/s');
        $param = json_encode($data, 255);
        $res = $this->directive->directiveUpd($param);
        if ($res['status'] == '修改成功' || $res['status'] == '添加成功') {
            $this->logger(90);
            jsonResult();
        } else {
            $this->logger(91);
            jsonError();
        }
    }

    public function getMaxIDAction()
    {
        $maxId = $this->directive->getMaxID();
        jsonResult(['maxId' => $maxId]);
    }

    public function getPluginIdAction()
    {
        $data = $this->directive->getPluginId();
        jsonResult($data);
    }

//    public function getPluginSidAction()
//    {
//        if (!empty(input('get.id'))) {
//            $redis = new \phpredis();
//            if ($redis->get('plugin_id-' . input('get.id'))) {
//                $data = json_decode($redis->get('plugin_id-' . input('get.id')), 1);
//            } else {
//                $data = $this->directive->getPluginSid(input('get.id'));
//                $redis->set('plugin_id-' . input('get.id'), json_encode($data, 256), 0, 0, 86400);
//            }
//
//            jsonResult($data);
//        }
//        jsonError('参数错误');
//    }

    public function searchPluginSidAction()
    {
        $data['id'] = input('get.id/d');
        $data['plugin_keyWords'] = input('get.plugin_keyWords/d');
        if(!empty($data)){
            $res = $this->directive->searchPluginSid($data);
            if(!empty($res)){
                jsonResult($res);
            }
            jsonError('未查到');
        }
    }


    public function getKingDomAction()
    {
        $data = $this->directive->getKingDoms();
        jsonResult($data);
    }

    public function getCategoryAction()
    {
        $data = $this->directive->getCategories();
        jsonResult($data);
    }

}

?>