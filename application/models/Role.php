<?php
namespace App\Models;

class Role extends Model{
    protected $tableName = 'role';
    protected $tablePrefix = 'user_';
    protected $pk = 'id';

    protected $_validate = array(
        array('rolename','','角色名称已经存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
    );


   //自动完成
    protected $_auto = array (
        array('rolename','decryptcode',3,'function'),
        array('rank','decryptcode',3,'function'),
        array('id','decryptcode',2,'function'),
        array('isdel','decryptcode',2,'function'),
    );

    //模型类
    protected $user,$userreference,$menu;

    // 回调方法 初始化模型
    protected function _initialize() {

        $this->user = new User();
        $this->userreference = new Userreference();
        $this->menu = new Menu();
    }

    /**
     * 获取列表
     */

    public function getList($where = [], int $page, int $pagesize, $order = 'a.id desc'): ?array
    {

     $result=$this->alias('a')->where($where)->order($order)->page($page,$pagesize)->select();
        /*
         获得资产附加属性
        */
        if (!\Tools::isEmpty($result)) {
            array_walk($result, function (&$arr) {
            	$rank = $this->rankName($arr['rank']);

                if(is_array($rank)){
                	$arr['rankname'] = $rank['rankname'];
                	$arr['menus'] = $rank['menus'];
                }else{
                	$arr['rankname'] = $rank;
                	$arr['menus'] = $rank;
                }
            });
        }

        return $result;
    }

    /**
	* 根据一批rank获取一批rankname和menus
    */
	public function rankName($rank)
	{
		if(!\Tools::isEmpty($rank)){

			$Menu = new Menu();
			$ret = $Menu->field('id,name,name_admin,pid')->where(['id'=>['in',$rank]])->order('priotity asc')->select();
			if(!\Tools::isEmpty($ret)){
				$result['rankname'] = implode(',',array_column($ret,'name')); 
                $result['menus'] = \Tree::instance()->formatTree($ret); 
                return $result;	
			}
			return '';

		}else{
			return '';
		}
	}
	/**
	* 根据条件获取总数
	*/
	public function getCount($where): ?int
	{
		return $this->alias('a')->where($where)->count();
	}
	/**
    * 获取属性
    */
    public function getRank($where = []): ?string
    {
        $result = $this->where($where)->getField('rank');        
        return $result;
    }
	/**
    * 获取属性
    */
    public function getIsdel($where = []): ?string
    {
        $result = $this->where($where)->getField('isdel');        
        return $result;
    }

    // 插入数据前的回调方法
    protected function _before_insert(&$data, $options) {
        
        if(!\Tools::isEmpty($data['rolename'])){
            $c = $this->where(['rolename'=>$data['rolename']])->count();
            if($c>0) jsonError('角色名称已经存在');
        }

    }
    // 更新数据前的回调方法
    protected function _before_update(&$data, $options) {

        //内置角色不允许操作
        if(!\Tools::isEmpty($data['id'])){
            $c = $this->where(['id'=>$data['id'],'isdel'=>0])->count();
            if($c > 0) jsonError('内置角色不允许修改');
        }
        //存在判断
        if(!\Tools::isEmpty($data['rolename'])){
            $c = $this->where(['rolename'=>$data['rolename']])->count();
            if($c>1) jsonError('角色名称已经存在');
        }
        
    }
    // 更新成功后的回调方法
    protected function _after_update($data, $options) {


        //同步所属角色的所有用户的权限
        $uid = array_column($this->user->field('id')->where(['rid'=>$data['id']])->select(),'id');
        if(!is_null($uid)){

            $menus = json_encode($this->menu->getAll(['id'=>['in',$data['rank']]]),256);

            $where = [
                'uid' => ['IN',$uid]
            ];
            $da = [
                'menuids' =>$data['rank'],
                'menus' => $menus,
            ];

            $this->userreference->where($where)->data($da)->save();
        }

    }
    // 删除数据前的回调方法
    protected function _before_delete($options) {

        if(is_string($options['where']['id'])){
            $c = $this->where(['id'=>['in',$options['where']['id']],'isdel'=>0])->count();
            if($c > 0) jsonError('内置角色不允许删除');
        }
        if(is_array($options['where']['id'])){
            if(!\Tools::isEmpty($options['where']['id'][1])){
                $c = $this->where(['id'=>['in',$options['where']['id'][1]],'isdel'=>0])->count();
                if($c > 0) jsonError('其中有内置角色不允许删除');
            }
        }   
    }
}