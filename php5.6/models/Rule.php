<?php
namespace App\Models;

/**
*   关联规则
*/
class Rule extends Model
{
    protected $tableName = 'rule';
    protected $tablePrefix = '';
    protected $pk = 'id';
       

    protected $field = [];
    protected $where = [];

    public function __construct($params = []){

        parent::__construct();

         //如果是普通用户登录
        if($_SESSION['uid'] >= 4) {
            $this->where['uid'] = ['in',[1,2,3,$_SESSION['uid']]];
        }else{
            $this->where['uid'] = ['between',[1,10000]];
        }
        $this->field['rule'] = 'id,rule_name,risk,status,iscustom,matching_mode,ctime,rule_type,group_id';

        $this->field['group'] = 'id,rule_name,rule_type,ctime,iscustom';

        $this->where['delete_status'] = ['eq',0];
    }

    public function getRuleGroupList($where,$page,$page_size){

        $search_where = [];

        if (!empty($where['group_id'])) {
            $search_where['id'] = ['in',$where['group_id']];
        }else{
             $search_where['group_id'] = ['eq',0];
        }

        $search_where['delete_status'] = $this->where['delete_status'];
        // $search_where['group_id'] = 1;
        //如果存在配置的UID 并且 展示全部的分类
        if ($where['type_mine'] == 0 && !empty($this->where['uid'])) {

            $search_where['uid'] = $this->where['uid'];

        }else{

            $search_where['uid'] = ['eq',$_SESSION['uid']];
        }

        $data['total_num'] =   strval($this->where($search_where)->count('*'));
   // echo $this->getLastSql();die;
        if ($data['total_num'] < 1 ) return false;

        $data['total_page'] = strval(ceil( $data['total_num']/$page_size));
      
        $data['list'] = $this->field($this->field['group'])->where($search_where)->order('id desc')->page($page,$page_size)->select();

        foreach ($data['list'] as $k => $v) {

            if (!empty($v['content'])) {
               $data['list'][$k]['content'] = json_decode($v['content'],256);
            }

        }

        return $data;
    }

    public function getRuleList($where,$page,$page_size){
        
        if (empty($where['group_id'])) return [];

        $search_where = [];

        $search_where['group_id'] = $where['group_id'];
 
        //如果存在配置的UID 并且 展示全部的分类
        if ($where['type_mine'] == 0 && !empty($this->where['uid'])) {

            $search_where['uid'] = $this->where['uid'];

        }else{

            $search_where['uid'] = ['eq',$_SESSION['uid']];
        }
        $search_where['delete_status'] = $this->where['delete_status'];
        foreach ($where as $k => $v) {

            if (!empty($v)) {
               
                switch ($k) {
                    case 'rule_name':
                       $search_where['rule_name']      = ['like',"%{$v}%"];
                        break;
                    case 'risk':
                       $search_where['risk']           = ['eq',"{$v}"];
                        break;
                    case 'matching_mode':
                        $search_where['matching_mode'] = ['eq',"{$v}"];
                        break;
                    case 'status':
                       $search_where['status']         = ['eq',"{$v}"];
                        break;
                    case 'iscustom':
                       $search_where['iscustom']       = ['eq',"{$v}"];
                        break;
                    default:
                       
                        break;
                }

            }
        }

        $data['total_num'] =   strval($this->where($search_where)->count('*'));
       // echo $this->getLastSql();die;
        if ($data['total_num'] < 1 ) return [];

        $data['total_page'] = strval(ceil( $data['total_num']/$page_size));
      
        $data['list'] = $this->field($this->field['rule'])->where($search_where)->order('id desc')->page($page,$page_size)->select();

        foreach ($data['list'] as $k => $v) {

            if (!empty($v['content'])) {
               $data['list'][$k]['content'] = json_decode($v['content'],256);
            }

        }

        return $data;

    }

    public function getAllRuleGroupBySearch($where = []){
      
        $search_where = [];
        $search_where['delete_status'] = $this->where['delete_status'];
        //如果存在配置的UID 并且 展示全部的分类
        if ($where['type_mine'] != 1 && !empty($this->where['uid'])) {

            $search_where['uid'] = $this->where['uid'];
        }else{

            $search_where['uid'] = $_SESSION['uid'];
        }

        if (!empty($where['group_name']) || !empty($where['group_type'])) {
        
            if(!empty($where['group_name'])) $search_where['rule_name'] = ['like',"%{$where['group_name']}%"];
            if(!empty($where['group_type'])) $search_where['rule_type'] = ['eq',"{$where['group_type']}"];
            $search_where['group_id'] = ['eq','0'];
            $data['group_id'] = $this->field('id')->where($search_where)->group('id')->select();
          
            if(empty($data['group_id'])) {echo json_encode(['errcode'=>1,'data'=>[],'msg'=>'未查询到规则组:'.$where['group_name'].'|'.$where['group_type']]);die();}

            $data['group_id']  =  array_column($data['group_id'], 'id');

            //删除掉防止变量污染
            unset($search_where['rule_name']);
            unset($search_where['rule_type']);
            unset($where['group_name']);
            unset($where['group_type']);
        }
    
        if (!empty($where['rule_name']) || !empty($where['risk']) || !empty($where['matching_mode']) || !empty($where['status']) || !empty($where['iscustom']) ) {
            if(!empty($where['rule_name']))     $search_where['rule_name']     = ['like',"%{$where['rule_name']}%"];
            if(!empty($where['risk']))          $search_where['risk']          = ['eq',"{$where['risk']}"];
            if(!empty($where['matching_mode'])) $search_where['matching_mode'] = ['eq',"{$where['matching_mode']}"];
            if(!empty($where['status']))        $search_where['status']        = ['eq',"{$where['status']}"];
            if(!empty($where['iscustom']))      $search_where['iscustom']      = ['eq',"{$where['iscustom']}"];
            //这里是查询规则    group_id > 0 的 为规则  
            $search_where['group_id'] = ['neq','0'];
            $data['rule_id'] = $this->field('group_id')->where($search_where)->group('group_id')->select();
            //echo $this->getLastSql();die;
            if(empty($data['rule_id'])) {echo json_encode(['errcode'=>1,'data'=>[],'msg'=>'未查询到规则']);die();}

            $data['rule_id']  =  array_column($data['rule_id'], 'group_id');
        }

        // 如果只有一个，那就只查一个，如果都有，那就取交集
        if (!empty($data['group_id']) && empty($data['rule_id'])) {
           return $data['group_id'];
        }elseif (empty($data['group_id']) && !empty($data['rule_id'])) {
           return $data['rule_id'];
        }else{
           return array_values(array_intersect($data['group_id'], $data['rule_id']));
        }

    }

    /**
     * @abstract 验证规则组/规则  名称是否存在
     * @param  [type] $rule_name [description]
     * @param  [type] $group_id  [description]
     * @return [type]            [description]
     */
    public function checkRuleName($rule_name,$group_id,$id){

        $info = $this->field('rule_name,id')->where(['delete_status'=>0,'uid'=>$_SESSION['uid'],'rule_name'=>$rule_name,'group_id'=>$group_id])->select();

        if (!empty($info[0]['rule_name']) && $info[0]['id'] != $id) {

           return '该名称已经被使用';
        }
      
        return 1;
    }
}
