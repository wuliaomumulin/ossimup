<?php

namespace App\Models;

class PluginSid extends Model{
    protected $tableName = 'plugin_sid';
    protected $tablePrefix = '';
    protected $pk = 'plugin_id';

    //自动完成
    protected $_auto = array (
        
    );

    protected $_validate = array(
        
        array('name','require','栏目名称不能为空'),
          
    );




    /**
    * 获取所有
    */

    public function getAll($where = [])
    {   
       
        $rs['config'] = [

            ['description'=>'事件类型','key'=>'sid'],
            ['description'=>'事件名称','key'=>'name'],
            ['description'=>'优先级','key'=>'priority'],
            ['description'=>'可靠度','key'=>'reliability'],
            ['description'=>'分类','key'=>'class_id_name'],
            ['description'=>'子类型','key'=>'subcategory_id_name'],
            ['description'=>'类型','key'=>'category_id_name'],
        ];


        $rs['list'] = $this->field('*')->where($where)->limit(0,200)->select();

        !$rs['list'] && $rs['list']  = []; 

        if (!empty($rs['list'])) {
            $class_id = array_unique(array_column($rs['list'], 'class_id'));
            $category_id = array_unique(array_column($rs['list'], 'category_id'));
            $subcategory_id = array_unique(array_column($rs['list'], 'subcategory_id'));

            $class_info = $this->table('classification')->field('id,name')->where(['id'=>['in', $class_id]])->select(['index'=>'id']);
            $category_info = $this->table('category')->field('id,name')->where(['id'=>['in', $category_id]])->select(['index'=>'id']);

            $subcategory_info = $this->table('subcategory')->field('id,name')->where(['id'=>['in', $subcategory_id]])->select(['index'=>'id']);
            foreach ($rs['list'] as $k => $v) {

                $rs['list'][$k]['class_id_name'] = $class_info[$v['class_id']]['name'];
                $rs['list'][$k]['subcategory_id_name'] = $subcategory_info[$v['subcategory_id']]['name'];
                $rs['list'][$k]['category_id_name'] = $category_info[$v['category_id']]['name'];
            }
        }
        return $rs;
    }


    public function getList($where = [],$page = 1,$page_size = 20)
    {   
       
        // $rs['config'] = [

        //     ['description'=>'事件类型','key'=>'sid'],
        //     ['description'=>'事件名称','key'=>'name'],
        //     ['description'=>'优先级','key'=>'priority'],
        //     ['description'=>'可靠度','key'=>'reliability'],
        //     ['description'=>'分类','key'=>'class_id_name'],
        //     ['description'=>'子类型','key'=>'subcategory_id_name'],
        //     ['description'=>'类型','key'=>'category_id_name'],
        // ];

       
        $rs['list'] = $this->field('plugin_id,sid,class_id,reliability,priority,name,aro,subcategory_id,category_id')->where($where)->page($page,$page_size)->select();

        !$rs['list'] && $rs['list']  = []; 

        if (!empty($rs['list'])) {
            $class_id = array_unique(array_column($rs['list'], 'class_id'));
            $category_id = array_unique(array_column($rs['list'], 'category_id'));
            $subcategory_id = array_unique(array_column($rs['list'], 'subcategory_id'));

            $class_info = $this->table('classification')->field('id,name')->where(['id'=>['in', $class_id]])->select(['index'=>'id']);
            $category_info = $this->table('category')->field('id,name')->where(['id'=>['in', $category_id]])->select(['index'=>'id']);

            $subcategory_info = $this->table('subcategory')->field('id,name')->where(['id'=>['in', $subcategory_id]])->select(['index'=>'id']);
            foreach ($rs['list'] as $k => $v) {

                $rs['list'][$k]['class_id_name'] = $class_info[$v['class_id']]['name'];
                $rs['list'][$k]['subcategory_id_name'] = $subcategory_info[$v['subcategory_id']]['name'];
                $rs['list'][$k]['category_id_name'] = $category_info[$v['category_id']]['name'];
            }
        }
        return $rs;
    }

}