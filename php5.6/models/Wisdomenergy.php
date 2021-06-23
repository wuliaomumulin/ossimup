<?php

namespace App\Models;

class Wisdomenergy extends Model
{

    protected $dbName = 'alienvault';
    protected $tableName = 'group';
    protected $tablePrefix = 'zhny_';
    protected $pk = 'id';

    public function GetMenu()
    {
        return $this->table('alienvault.zhny_dict')->select();
    }

    public function GetMenuData($id)
    {
        return $this->where(['id' => $id])->GetField('menus');
    }

    public function CreateItem($param)
    {
        return $this->add($param);
    }

    public function SaveMenu($param)
    {
        return $this->where(['id' => $param['id']])->data($param)->save();
    }

    public function SaveMenuData($param)
    {

        return $this->where(['id' => $param['id']])->data($param)->save();
    }

    public function GetMenuModel($param)
    {
        return $this->where(['id' => $param])->find();
    }

    public function GetHosts($where = [])
    {
        return $this->field('a.id,a.name,a.type,b.type typename')->alias('a')->join('zhny_group_type b on a.type=b.id')->where($where)->select();
    }

    public function GetFather($param)
    {
        return $this->table('alienvault.zhny_dict')->where($param)->select();
    }

    public function GetItem($id)
    {
        $result = $this->table('alienvault.zhny_dict')->where(['id' => $id])->find();
        $result['value'] = '0';
        return $result;
    }

    //es新版
    public function UpdateMenu($old_menus,$param)
    {



        //源数据
        $old_menus = json_decode($old_menus, 1);
        $old_ids = [];
        foreach ($old_menus as $k => $v) {
            if (!empty($v['children'])) {
                foreach ($v['children'] as $key => $val) {
                    array_push($old_ids, $val['id']);
                }
            }
        }
        //新数据
        $new_menus = json_decode($param['menus'], 1);
        $new_ids = [];
        foreach ($new_menus as $k => $v) {
            if (!empty($v['children'])) {
                foreach ($v['children'] as $key => $val) {
                    array_push($new_ids, $val['id']);
                }
            }
        }
        //并集
        $data = array_unique(array_merge($old_ids, $new_ids));
        //旧数据对比差集
        //$res = array_diff($data, $old_ids);    // 要新增的
        $res = array_diff($data, $new_ids);    //要删除的
        if (!empty($res)) {
            $names = [];
            foreach ($res as $k => $v) {
                $data = $this->table('alienvault.zhny_dict')->where(['id' => $v, 'type' => '企业上报'])->GetField('name');
                    if(!is_null($data)){
                        $names[] =  $data;
                    }
            }
            return $names;
        }
        // $res2 = array_diff($data, $new_ids);    //要删除的

//        if(!empty($res1)){
//            foreach ($res1 as $new_cid) {
//                $pid = $this->table('alienvault.zhny_dict')->where('id ='.$new_cid)->GetField('pid');
//                $add_pid=true;
//                foreach ($old_detail as $b => $c) {
//                    if($c['id'] == $pid){
//                        $add_pid=false;
//                        array_push($old_detail[$b]['children'],$this->GetItem($new_cid));
//                    }
//                }
//                if($add_pid){
//                    $parent=$this->GetItem($pid);
//                    $parent['children']=array();
//                    array_push($parent['children'], $this->GetItem($new_cid));
//                    array_push($old_detail,$parent);
//                }
//            }
//        }

//        if(!empty($res2)){
//            //从老数据里 或者上步骤添加后的数据里去除删除的数据
//            foreach ($old_detail as $k => $v){
//                if(!empty($v['children'])){
//                    foreach ($v['children'] as $key => $val){
//                        if(in_array($val['id'],$res2)){
//                            unset($old_detail[$k]['children'][$key]);
//                        }
//                    }
//                }
//
//                //孩子删除完了  要把顶级标题父类也删除
//                if(empty($old_detail[$k]['children'])){
//                    unset($old_detail[$k]);
//                }
//            }
//        }

        //  return $res;
    }

    //原版
//    public function UpdateMenu($param)
//    {
//
//        $detail = $this->where(['id' => $param['id']])->GetField('detail');
//
//        //源数据
//        $old_detail = json_decode($detail, 1);
//        $old_ids = [];
//        foreach ($old_detail as $k => $v) {
//            if (!empty($v['children'])) {
//                foreach ($v['children'] as $key => $val) {
//                    array_push($old_ids, $val['id']);
//                }
//            }
//        }
//        //新数据
//        $new_detail = json_decode($param['detail'], 1);
//        $new_ids = [];
//        foreach ($new_detail as $k => $v) {
//            if (!empty($v['children'])) {
//                foreach ($v['children'] as $key => $val) {
//                    array_push($new_ids, $val['id']);
//                }
//            }
//        }
//        //并集
//        $data = array_unique(array_merge($old_ids, $new_ids));
//
//        //旧数据对比差集
//        $res1 = array_diff($data, $old_ids);    // 要新增的
//        $res2 = array_diff($data, $new_ids);    //要删除的
//
//        if(!empty($res1)){
//            foreach ($res1 as $new_cid) {
//                $pid = $this->table('alienvault.zhny_dict')->where('id ='.$new_cid)->GetField('pid');
//                $add_pid=true;
//                foreach ($old_detail as $b => $c) {
//                    if($c['id'] == $pid){
//                        $add_pid=false;
//                        array_push($old_detail[$b]['children'],$this->GetItem($new_cid));
//                    }
//                }
//                if($add_pid){
//                        $parent=$this->GetItem($pid);
//                        $parent['children']=array();
//                        array_push($parent['children'], $this->GetItem($new_cid));
//                        array_push($old_detail,$parent);
//                    }
//            }
//        }
//
//        if(!empty($res2)){
//            //从老数据里 或者上步骤添加后的数据里去除删除的数据
//            foreach ($old_detail as $k => $v){
//                if(!empty($v['children'])){
//                    foreach ($v['children'] as $key => $val){
//                        if(in_array($val['id'],$res2)){
//                            unset($old_detail[$k]['children'][$key]);
//                        }
//                    }
//                }
//
//                //孩子删除完了  要把顶级标题父类也删除
//                if(empty($old_detail[$k]['children'])){
//                    unset($old_detail[$k]);
//                }
//            }
//        }
//        //exit();
//        return json_encode($old_detail,256);
//    }

    public function DelMenu($id)
    {
        return $this->where(['id' => $id])->delete();
    }


}