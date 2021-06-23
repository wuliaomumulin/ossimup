<?php
use App\Models\PluginSid;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Classification;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class PluginsidController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
 
    public function init()
    {
        $this->config = \Yaf\Registry::get("config");
        $this->model = new PluginSid();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
         
    }

    public function querylistAction()
    {  
       
        $where = array();

        $id = input('get.id',0,'int');

        $page = input('get.page',1,'int');
        $page_size = input('get.page_size',20,'int');

        if(!empty($id)){
        	$where['plugin_sid.plugin_id'] = ['eq',$id];
        }else{
            jsonError('无效的参数');
        }

        /*临时的查询*/

        $where['sid'] = input('get.sid',0,'int');
        $name = input('get.name','');
        if(!empty($name)){
            $where['name'] = ['like','%'.$name.'%'];
        }
        $where['priority'] = input('get.priority',0,'int');
        $where['reliability'] = input('get.reliability',0,'int');
        $where['class_id'] = input('get.class_id_name',0,'int');
        $where['category_id'] = input('get.category_id_name',0,'int');

        foreach ($where as $k=> $v) {
          if(empty($v) ) unset($where[$k]);
        }
       
        $rs['total_num'] = $this->model->where($where)->count();

        $rs['total_page'] = strval(ceil($rs['total_num']/$page_size));

        if ($page > 0 && $page <= $rs['total_page']){

            $rs['list']= $this->model->getList($where,$page,$page_size);
        }else{
            $rs['list']['list'] = [];
        }

        $rs['list']['config'] = json_decode(file_get_contents('./list_conf/pluginDetails.json'),1);

        $Classification = (new Classification())->field('id,name')->select();

        $Category = (new Category())->field('id,name')->select();

        foreach ($rs['list']['config'] as $k => $v) {
           
            if ($v['name'] == 'class_id_name') {
                    
              $rs['list']['config'][$k]['valueField'] = implode(',', array_column($Classification, 'id'));
              $rs['list']['config'][$k]['textField'] = implode(',', array_column($Classification, 'name'));
            }

            if ($v['name'] == 'category_id_name') {
                    
                $rs['list']['config'][$k]['valueField'] = implode(',', array_column($Category, 'id'));

                $rs['list']['config'][$k]['textField'] = implode(',', array_column($Category, 'name'));
            }

        }



        jsonResult($rs);
    }

    /**
     * @abstract 添加编辑子插件
     * @return [type] [description]
     */
    public function saveDataAction(){

        $data['plugin_id'] = decryptcode(input('post.plugin_id'))?decryptcode(input('post.plugin_id')):0;

        !$data['plugin_id'] && jsonError('无效的参数');

        $data['sid'] = decryptcode(input('post.sid'))?decryptcode(input('post.sid')):0;

        $data['class_id'] = decryptcode(input('post.class_id'))?decryptcode(input('post.class_id')):0;
        $data['subcategory_id'] = decryptcode(input('post.subcategory_id'))?decryptcode(input('post.subcategory_id')):0;
        $data['category_id'] = decryptcode(input('post.category_id'))?decryptcode(input('post.category_id')):0;
        $data['reliability'] = decryptcode(input('post.reliability'))?decryptcode(input('post.reliability')):1;
        $data['priority'] = decryptcode(input('post.priority'))?decryptcode(input('post.priority')):1;

        $data['name'] = decryptcode(input('name'));
        $old_id  = decryptcode(input('old_sid'))?decryptcode(input('old_sid')):0;
        !$data['name'] && jsonError('无效的参数');

        $edit = decryptcode(input('post.edit'))?decryptcode(input('post.edit')):0;
        $data['plugin_ctx'] = '0x00000000000000000000000000000000';
        $data['aro'] = '0.0000';
        
        if ($edit == 1) {
            $rs =  $this->model->where("sid = ".$old_id." AND plugin_id = ".$data['plugin_id'])->save($data);
            if(!empty($rs)){
                $this->logger(84);
            }else{
                $this->logger(85);
            }
        }else{

            $check = $this->model->where("sid = ".$data['sid']." AND plugin_id = ".$data['plugin_id'])->find();
           
            if($check) jsonError('重复的子ID');
            $rs =  $this->model->add($data);
            if(!empty($rs)){
                $this->logger(84);
            }else{
                $this->logger(85);
            }
        }

        jsonResult($rs);
    }

    /**
     * @abstract 编辑的时候 检测是否存在此ID
     * @return [type] [description]
     */
    public function checkSidAction(){

        $data['plugin_id'] = input('get.plugin_id',0,'int');

        !$data['plugin_id'] && jsonError('无效的参数');

        $data['sid'] = input('get.sid',0,'int');

        !$data['sid'] && jsonError('无效的参数');

        $check = $this->model->where("sid = ".$data['sid']." AND plugin_id = ".$data['plugin_id'])->find();

        if ($check) {
            jsonResult(['is_find'=>'1']);
        }
         jsonResult(['is_find'=>'0']);
    }

    public function getSubcategoryListAction(){

        $name = input('get.name');
        $id = input('get.category_id');

        if (!empty($name)) {
             $where['name'] = ['like','%'.$name.'%'];
        }elseif(!empty($id)){
              $where['cat_id'] = ['eq',$id];
        }else{
            $where = [];
        }

        $list = (new Subcategory())->field('id as subcategory_id,name')->where($where)->select();

        jsonResult($list);
    }

    public function getCategoryListAction(){

        $name = input('get.name');

        if (!empty($name)) {
             $where['name'] = ['like','%'.$name.'%'];
        }else{
            $where = [];
        }

        $list = (new Category())->field('id as category_id,name')->where($where)->select();

        jsonResult($list);
    }

    public function getClassificationListAction(){

        $name = input('get.name');

        if (!empty($name)) {
             $where['name'] = ['like','%'.$name.'%'];
        }else{
            $where = [];
        }

        $list = (new Classification())->field('id,name')->where($where)->select();

        jsonResult($list);
    }

    public function delDataAction(){
        $plugin_sid = input('get.plugin_sid',0,'int');
        $plugin_id = input('get.plugin_id',0,'int');

        if(!$plugin_sid || !$plugin_id){
            jsonError('无效的参数');
        }

        $rs = $this->model->where("sid = ".$plugin_sid." AND plugin_id = ".$plugin_id)->delete();
        if(!empty($rs)){
            $this->logger(86);
        }else{
            $this->logger(87);
        }

        jsonResult($rs);
    }
    /***
    * 前置方法，取出查询的唯一字段
    */
    public function prev(){
        $request = input('post.');
        unset($request['page'],$request['page_size'],$request['begindate'],$request['enddate']);
        return $request;
    }
}