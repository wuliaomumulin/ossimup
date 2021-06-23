<?php
use App\Models\Rule;
/**

 */
class RuleController extends Base
{
    protected $model = null;
    
    public function init()
    {
        parent::init();
        
        $this->model = new Rule();
    }

    //列表页面
    public function indexAction()
    {
        $model = $this->model;
        $treedata = $model->field('id,title as name,pid,status')->order('rank asc')->select();
        $tree = Tree::instance()->init($treedata, 'pid');
        $treeList = Tree::instance()->getTreeArray(0);
        $treejson = json_encode($treeList);
        $treejson = str_replace('childlist','children',$treejson);
        echo $treejson;
        $this->getView()->assign("treejson", $treejson);
        $this->getView()->display('rule/index.html');
    }

    //信息添加
    public function editAction()
    {
        $id = input('id',0);
        $pid = input('pid',0);
        $model = $this->model;
        $info = $model->find($id);
        if ($id==0 && $pid>0){
            $info['pid'] = $pid;
        }
        $this->getView()->assign("info", $info);
        //父级菜单
        $treedata = $model->field('id,pid,title')->where("`status`='1' and `type`='menu' ")->order('rank asc')->select();
        $tree = Tree::instance()->init($treedata, 'pid');
        $treeList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        $menuList = [0 => '无'];
        foreach ($treeList as $k => &$v)
        {
            $menuList[$v['id']] = $v['title'];
        }
        $this->getView()->assign("menuList", $menuList);
        $this->getView()->display('rule/edit.html');
    }
    
}
