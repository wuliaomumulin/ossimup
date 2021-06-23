<?php
namespace App\Models;

class Category extends Model
{
    protected $tableName = 'category';
    protected $tablePrefix = '';
    protected $pk = 'id';
    
  
    
    //自动完成
    protected $_auto = array (
         
        );
    
    //public $cate_type = ['page'=>'页面','article'=>'文章','link'=>'链接','product'=>'产品'];

}
