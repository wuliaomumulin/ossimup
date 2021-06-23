<?php
namespace App\Models;

class Role extends Model{
    protected $tableName = 'role';
    protected $tablePrefix = 'user_';
    protected $pk = 'id';
    protected $builtIn = [1,2,3];//内置角色

    protected $_validate = array(
        array('rolename','require','角色名称不能为空'),
    );


   //自动完成
    protected $_auto = array (
        array('rolename','decryptcode',3,'function'),
        array('rank','decryptcode',3,'function'),
        array('id','decryptcode',2,'function'),
        array('isdel','decryptcode',2,'function'),
    );

    /**
     * 获取列表
     */

    public function getList($where = [], $page, $pagesize, $order = 'a.id desc')
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
	public function getCount($where)
	{
		return $this->alias('a')->where($where)->count();
	}
	/**
    * 获取属性
    */
    public function getRank($where = [])
    {
        $result = $this->where($where)->getField('rank');        
        return $result;
    }
	/**
    * 获取属性
    */
    public function getIsdel($where = [])
    {
        $result = $this->where($where)->getField('isdel');        
        return $result;
    }

    // 删除数据前的回调方法
    protected function _before_delete($options) {
        //是否内置,默认1,2,3是内置用户,如果是的话不允许删除
        if(is_string($options['where']['id']) and in_array($options['where']['id'],$this->builtIn)){
            jsonError('内置角色不允许删除');
        }
        if(is_array($options['where']['id'])){
            if(!\Tools::isEmpty($options['where']['id'][1])){
                if(stripos($options['where']['id'][1],',') > -1){
                    if(!empty(array_intersect(explode(',',$options['where']['id'][1]),$this->builtIn))){
                        jsonError('其中有内置角色不允许删除');
                    }
                }
            }
        }   
    }
}