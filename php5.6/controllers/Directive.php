<?php
use App\Models\Directive;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
class DirectiveController extends Base{
    protected $directive;

    public function init(){
        parent::init();
        $this->directive=new Directive();
    }

   //查
    public function getdatalistAction()
    {
        $datas= $this->directive->getDataList();
        jsonResult($datas);
    }

   //大规则的新增和编辑
    public function directiveAddAction()
    {
        $data['sid'] = input('post.sid/s','');
        $data['attr'] = input('post.attr/a');
        $data['file_name'] = input('post.file_name/s');
        $param = json_encode($data,255);
        $res = $this->directive->directiveAdd($param);

        if($res['status'] == '添加成功' || $res['status'] === '修改成功'){
            $this->logger(90);
            jsonResult();
        }else{
            $this->logger(91);
            jsonError();
        }

    }

    //删
    public function directiveDelAction()
    {
        $data['sid'] = input('get.sid/s');
        $data['file_name'] = input('get.file_name/s');

        $param = json_encode($data,255);
        $res = $this->directive->directiveDel($param);
        if($res['status'] == '删除成功'){
            $this->logger(92);
            jsonResult();
        }else{
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
        $param = json_encode($data,255);
        $res = $this->directive->directiveUpd($param);
        if($res['status'] == '修改成功' || $res['status'] == '添加成功'){
            $this->logger(90);
            jsonResult();
        }else{
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

    public function getPluginSidAction()
    {
        if(!empty(input('get.id'))){
            $data = $this->directive->getPluginSid(input('get.id'));
            jsonResult($data);
        }
        jsonError('参数错误');
    }

}
?>