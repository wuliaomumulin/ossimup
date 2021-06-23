<?php
use App\Models\Menu;
use App\Models\Group;
use App\Models\Userreference;
use App\Models\Company;
/**
 * @name MenuController
 * @desc
 */
class MenuController extends Base
{
    protected $model = null;
    protected $uid=null;
    protected $rid = null;
    protected $role=null;
    
    public function init()
    {
        parent::init();
        $this->uid =$_SESSION['uid'];
        $this->rid =$_SESSION['rid'];
        $this->user_reference = $_SESSION['user_reference'];
        $this->role = [1,2,3];//系统内置角色
        $this->model = new Menu();
    }

    public function indexAction()
    {
        $uid=$this->uid;
        $rid=$this->rid;
        $rids = $this->role;
        $reference = new Userreference();
        $model = $this->model;
        $now_menu_id = $reference->field('menuids,menus,content,menu_id,comid')->where(['uid' => $uid])->find();
        if(!empty($uid) && !in_array($rid,$rids)){
            $where['id'] = array('in',$now_menu_id['menuids']);

            $treedata = $this->model->field('id,name,pid,name_en,icon,router,name_admin')->where($where)->order('priotity asc')->select();
            $tree = Tree::instance()->formatTree($treedata);  
        }else{
            $where['id'] = array('in',$now_menu_id['menuids']);
            $treedata = $this->model->field('id,name,pid,name_en,icon,router,name_admin')->where($where)->order('priotity asc')->select();
            $tree = Tree::instance()->formatTree($treedata);
        }

        echo resultstatus(0,'success',$tree);
    }
    //
    public function getmenuAction()
    {
        $uid=$this->uid;
        $reference = new Userreference();
        $model = $this->model;
        $treedata = $model->field('id,name,pid,name_admin')->order('priotity asc')->select();
        $tree = Tree::instance()->formatTree($treedata);
        echo resultstatus(0,'success',$tree);
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
        $treedata = $model->field('id,pid,name')->where("`status`='1' ")->order('type asc,rank asc')->select();
        $tree = Tree::instance()->init($treedata, 'pid');
        $treeList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
        $menuList = [0 => '无'];
        foreach ($treeList as $k => &$v)
        {
            $menuList[$v['id']] = $v['name'];
        }
        $this->getView()->assign("menuList", $menuList);
        $this->getView()->assign("cate_type", $model->cate_type);
        $this->getView()->display('category/edit.html');
    }
    /**
    * 判断是安全管理员或系统操作员
    */
    private function checkSecAdmin(){
        if(in_array($this->rid,[2,4])){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}
