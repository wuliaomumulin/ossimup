<?php
namespace App\Models;

class Menu extends Model
{
    protected $tableName = 'menu';
    protected $tablePrefix = 'yaf_';
    protected $pk = 'id';
    
    protected $_validate = array(
            array('name','require','栏目名称不能为空'),
            array('name','','栏目名称已经存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
        );

    /**
	* 初始化一个Menus
    */
    public function initMenus($where){
    	$ret = $this->field('id,name,name_admin,pid')->where($where)->select();
    	if(!\Tools::isEmpty($ret)){
    		//$result['menus'] = \Tree::instance()->formatTree($ret); 
    		return serialize($ret); 
    	}else{
    		return '';
    	}
    }

}
