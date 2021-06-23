<?php
namespace App\Models;

class User extends Model
{
    protected $tableName = 'user';
    protected $tablePrefix = 'yaf_';
    protected $pk = 'id';
    protected $builtIn = [1,2,3];//内置用户 

    protected $_validate = array(
            array('username','require','用户名称不能为空'),
            array('username','','用户名称已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
            array('nickname','require','用户昵称不能为空'),
        );        

    //自动完成
    protected $_auto = array (
            array('createtime','gettime',1,'callback',),
            array('password','_mymd5',3,'callback'),
            array('password','',self::MODEL_BOTH,'ignore'),//为空时候不验证
            array('loginip','get_client_ip',3,'function',1),
            array('status','0'),
            array('birthday','getdata',1,'callback'),
            array('logintime','gettime',3,'callback'),

            array('id','decryptcode',2,'function'),
            array('rid','decryptcode',3,'function'),
            array('mobile','decryptcode',3,'function'),
            array('email','decryptcode',3,'function'),
            array('username','decryptcode',3,'function'),
            array('nickname','decryptcode',3,'function'),
            array('group_id','decryptcode',2,'function'),
            array('birthday','decryptcode',2,'function'),
            array('avatar','decryptcode',2,'function'),
            array('status','decryptcode',2,'function'),
            array('level','decryptcode',2,'function'),
            array('createtime','decryptcode',2,'function'),
            array('gender','decryptcode',2,'function'),
            // array('menuids','decryptcode',2,'function'),
            // array('userreferenceid','decryptcode',2,'function'),
            // array('rolename','decryptcode',2,'function'),

        );

    protected $_map = array(
            // 'name' =>'username', // 把表单中name映射到数据表的username字段
            // 'mail'  =>'email', // 把表单中的mail映射到数据表的email字段
        );
    
	/**
     * 校验用户登录
     * @param type $username 111
     * @param type $userpwd
     * @param type $vcode
     * @throws \Exception
     */
    public function checkLogin($username,$userpwd,$vcode,$login_num = 0)
    {
         //超过三次出验证码
        if ($login_num >= 3 ) {
             if (strtolower($vcode)!== $_SESSION['captcha']&&!empty($_SESSION['captcha'])){
                    throw new \Exception("验证码不正确",'001');
            } 
        }
        //进行数据保存
        if ($login_num == 5) {
           throw new \Exception("输错次数过多,ip已经被限制15分钟内禁止登陆",'003');
        }
        if ($username==""||$userpwd==""){
            throw new \Exception("用户名或密码不能为空",'002');
        }

        $data = array('username'=>$username );
        $result = $this->field("id,nickname,username,rid,password,salt,email,mobile,birthday,status,comid")->where($data)->find();
     
        if ($result['status'] == 0) {

            self::set_login_num($username,$login_num);

            throw new \Exception("用户名或密码输入错误",'002');
            //throw new \Exception("该用户未授予登陆权限",'002');
        }
        if ($result && $result['password']==mymd5($userpwd))
        {
            $this->where($data)->save(array('logintime'=>parent::gettime(),'loginip'=>get_client_ip()));
            $_SESSION['uid'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['nickname'] = $result['nickname'];
            $_SESSION['company_id'] = $result['comid'];
            $_SESSION['rid'] = $result['rid'];
            //查出用户的关联信息，并且存入session
            if($reference = $this->table('user_reference')->where(['uid'=>$result['id']])->find()){
                $_SESSION['user_reference'] = $reference;
            }
            //查出公司icon
            $result['icon'] = $this->table('company')->where(['comid'=>$result['comid']])->getField('icon');
            $config = $this->table('system_config')->field('value,name')->where(['name'=>['in',['DEFAULT_ROULTER','ES_SHOW_TIME','ES_HOSTNAME']],'user_id'=>['in',[0,$result['id']]]])->select(['index'=>'name']);
            $_SESSION['es_show_time'] = $config['ES_SHOW_TIME']['value'];
            $_SESSION['ES_HOSTNAME'] = $config['ES_HOSTNAME']['value'];
            $result['es_show_time'] = intval($config['ES_SHOW_TIME']['value'])*1000;
            $result['DEFAULT_ROULTER'] =$config['DEFAULT_ROULTER']['value']?$config['DEFAULT_ROULTER']['value']:'';
            unset($result['password']);
            return $result;
        } else {
           
            throw new \Exception("用户名或密码输入错误",'004');
        }
    } 


    public function getsensorV2($pid = 0,$eq = 'parid'){
        return $this->formatTreeV3($pid,$eq);
    }
    /**
     * @abstract 获得用户授权树  版本3
     * @author wangxiaohui
     * @return [type] [description]
     */
    private function formatTreeV3($id = 0){
     
        $redis = new \phpredis();

        $key   = 'Model-User-formatTreeV3';

        $company = $redis->get($key);

        if (!$company) {

            $company=$this->table('company')->field("CONCAT('c',comid) as id,comid,parid,comname name")->select(['index'=>'comid']);

            $redis->set($key,json_encode($company),0,0,10);

        }else{
            $company = json_decode($company,256);
        }
        $rs = [];
        foreach ($company as $k => $v) {
            if ($v['parid'] == $id) {
               $rs[$k] = $v;
               // 返回 旗下所有的公司以及电厂
               $rs[$k]['children'] = array_merge(self::formatTreeV3($v['comid']),self::getFactoryByCompany($v['comid']));
             
            }
        }

        return array_values($rs);
    }

    private function getFactoryByCompany($id = 0 ){

        $redis = new \phpredis();

        $key   = 'Model-User-getFactoryByCompany';

        $factory = $redis->get($key);

        if (!$factory) {
           
            $factory = $this->table('factory')->field("CONCAT('f',facid) as  id,facid,facname name,comid")->select(['index'=>'facid']);

            $redis->set($key,json_encode($factory),0,0,10);

        }else{
            $factory = json_decode($factory,256);
        }

        if ($id != 0) {
            $rs = [];
            foreach ($factory as $k => $v) {
               if ($v['comid'] == $id) $rs[] = $v;
            }
            return $rs;
        }

        return $factory;
    }
    // 插入数据前的回调方法
    protected function _before_insert(&$data, $options) {

    }

    // 插入成功后的回调方法
    protected function _after_insert($data, $options) {
        //更新user_reference
        if(!\Tools::isEmpty($data['rid'])){
            $da['uid'] = $this->getLastInsID();
            $Role = new Role();
            $Menu = new Menu();
            $da['menuids']=$Role->getRank(['id'=>$data['rid']]);
            $da['menus']= $Menu->initMenus(['id'=>['in',$da['menuids']]]);
            $Userreference = new Userreference();
            $Userreference->data($da)->add();
        }
        
    }
    // 更新数据前的回调方法
    protected function _before_update(&$data, $options) {
        //是否内置,默认1,2,3是内置用户,如果是的话不允许删除
        if(!empty($data['id']) and in_array($data['id'],$this->builtIn)){
            jsonError('内置用户不允许编辑');
        }
    }

    // 更新成功后的回调方法
    protected function _after_update($data, $options) {
         //更新user_reference;
        if(!\Tools::isEmpty($data['rid'])){
            $whe['uid'] = $data['id'];
            $Role = new Role();
            $Menu = new Menu();
            $da['menuids']=$Role->getRank(['id'=>$data['rid']]);
            $da['menus']= $Menu->initMenus(['id'=>['in',$da['menuids']]]);
            $Userreference = new Userreference();
            $Userreference->where($whe)->data($da)->save();
        }
        
    }
    // 删除数据前的回调方法
    protected function _before_delete($options) {
        //是否内置,默认1,2,3是内置用户,如果是的话不允许删除
        if(is_string($options['where']['id']) and in_array($options['where']['id'],$this->builtIn)){
            jsonError('内置用户不允许删除');
        }
        if(is_array($options['where']['id'])){
            if(!\Tools::isEmpty($options['where']['id'][1])){
                if(stripos($options['where']['id'][1],',') > -1){
                    if(!empty(array_intersect(explode(',',$options['where']['id'][1]),$this->builtIn))){
                        jsonError('其中有内置用户不允许删除');
                    }
                }
            }
        }
    }
    // 删除成功后的回调方法
    protected function _after_delete($data, $options) {
        //更新user_reference
        $whe['uid'] = $data['id'];
        $Userreference = new Userreference();
        $Userreference->where($whe)->delete();

    }
    /*
    * 设置过期验证
     @params $keywords 当前关键字
     @params $login_num 当前频次
    */
    public function set_login_num($keywords,$login_num){
        $redis = new \phpredis();
        
        $key = get_client_ip().$keywords;
        
        ++$login_num;
        
        $redis->set($key,$login_num,0,0,60);  
    }
    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
    public function getModelName() {
        return '用户管理';
    }

    /**
    密码加解密
    */
    public function _mymd5($password=''){
        if(empty($password)) return $password;
        $Aes = new \Aes();
        return mymd5($Aes->decrypt($password));
    }
    /*
    * 得到校验次数
    */
    public function get_login_num(){
        $redis = new \phpredis();
        $key = get_client_ip().$_SESSION['username'];
        $login_num = $redis->get($key)?$redis->get($key):0;
        return $login_num;
    }
}
