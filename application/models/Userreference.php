<?php
namespace App\Models;

class Userreference extends Model{
    protected $tableName = 'user_reference';
    protected $tablePrefix = '';
    protected $pk = 'id';

	//自动完成
    protected $_auto = array (
            array('id','decryptcode',2,'function'),
            array('uid',"decryptcode",2,'function'),
            array('menus',"__menus",3,'callback'),
            array('menuids','__menuids',3,'callback'),
            array('theme_ids',"__theme_ids",3,'callback'),
        );
      protected function __menuids($val = ''){
        if(empty($val)) return $val;
        $val = implode(',',json_decode(\Aes::decrypt($val)));
        return $val;
      } 

      protected function __menus($val= ''){
        if(empty($val)) return $val;
        $val = serialize(json_decode(\Aes::decrypt($val),true));
        return $val;
      }
      protected function __theme_ids($val=''){
        if(empty($val)) return $val;
        $val = \Aes::decrypt($val);
        return $val;
      }
 
    public function getfacidbyuid($uid = 0){

      if ($uid == 0) {
         return 1;
      }

      $redis = new \phpredis();

      $key = 'Models-Userreference-getfacidbyuid-'.$uid;

      $rs = $redis->get($key);

      if (empty($rs)) {

        $where = [];
        
        $resu = $this->field('facid')->where(['uid'=>['eq',$uid]])->find();

        if (empty($resu)) {
            return false;
        }

        $rs = $resu['facid'];

        if ($rs == 'all') {
           $rs = 1;
        }else{
          $rs = explode(',', $rs)[0];
        }
        $redis->set($key,$rs,0,0,300);

      }

      return $rs; 

    }

    /**
     * @abstract 展示单个用户名下全部的被授权的采集器平台 信息 用于查询ES
     * @author wangxiaohui
     * @param  integer $uid  用户ID
     * @return [type]       [description]
     */
    public function getuserassetpower($uid = 0 ){

      $redis = new \phpredis();

      $key = 'Models-Userreference-getuserassetpower-'.$uid;

      $rs = $redis->get($key);

      if (!$rs) {

          if ( $_SESSION['user_reference']['facid'] == 'all') {
              $rs  = 'all';
              $redis->set($key,json_encode($rs),0,0,600);
          }elseif ( $_SESSION['user_reference']['facid'] == '') {
              $rs  = '';
          }else{

              //$rs = $this->table('asset')->field('es_id,asset_type,factory_id,company_id,asset_id')->where(['factory_id'=>['in', $_SESSION['user_reference']['facid']],'es_id'=>['neq'," "],"asset_type"=>["between",[88,92]]])->select();
          
              $rs = $this->table('asset')->field('es_id,asset_type,factory_id,company_id,asset_id')->where(['factory_id'=>['in', $_SESSION['user_reference']['facid']],'es_id'=>['neq'," "]])->select();
              $redis->set($key,json_encode($rs),0,0,600);
          }

      }else{
        $rs = json_decode($rs,1);
      }

      return $rs;
    }

     // 插入数据前的回调方法
    protected function _before_insert(&$data, $options) {

    }

    // 更新数据前的回调方法
    protected function _before_update(&$data, $options) {

    }
}