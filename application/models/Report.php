<?php
namespace App\Models;
//报表表
class Report extends Model
{
    protected $tableName = 'report';
    protected $tablePrefix = 'user_';
    protected $pk = 'id';
    
    protected $_validate = array(
            array('name','1,80','报表名称不合法，必须1~80个字符',0,'length'),
        );
    protected $_auto = array (
        array('uid','getuid',3,'callback'),
        array('thumb','_getthumb',self::MODEL_BOTH,'callback'),
        array('thumb','',self::MODEL_BOTH,'ignore'),//为空时候不验证
        array('content',"_json_encode",self::MODEL_BOTH,'callback'),
        array('content','',self::MODEL_BOTH,'ignore'),//为空时候不验证暂无资产
        array('is_mine', '1')
    );
 
    //获得全部报表
    public function getallreport($fields,$where=[],$page,$pagesize){
        //如果是大屏查询 

        /*
        * 将和当前用户唯一标识相匹配的条件、内置模板的条件都进行拼装,并且放入查询条件
        */
        $where[] = ['uid'=>$this->getuid(),'is_mine'=>0,'_logic'=>'OR'];
        /*
            一些公共基础属性
            分页情况
        */
        $result['total_num'] = (int)$this->where($where)->count();//总条数
        $result['total_page'] = ceil($result['total_num']/$pagesize);//总页数
        $result['page'] = $page+1;//当前页
        $result['page_size'] = $pagesize;//查询条数
        //数据主体
        $result['list'] = $this->field($fields)->where($where)->page($page, $pagesize)->select();
        //取出关联
        $result['is_default_screen'] = $this->table('user_reference')->where(['uid'=>$this->getuid()])->getField('menu_id');

        return $result;
    }

    /*
    *获取thumb图片url
    */
    public function _getthumb($thumb=''){
        if(empty($thumb)) return '';
        $upload = new \spUploadFile();
        return $upload->upload_file($_FILES['file'],"jpg|png|gif",'');         
    }
    /**
    * 尽可能的将数组中的string元素转换为int元素并且保存
    */
    public function _json_encode(){
        $content = input('post.content/a','');
        if(empty($content)) return '';
        array_walk_recursive($content,function(&$val,$key){
                //如果不是，就将数字元素转换为数字类型
                $val = is_numeric($val) ? (int)$val : $val;
        });
        return json_encode($content);
    }
    // 插入数据前的回调方法
    protected function _before_insert(&$data, $options) {
        //处理thumb

    }
    // 更新数据前的回调方法
    protected function _before_update(&$data, $options) {

    }

    // 插入成功后的回调方法
    protected function _after_insert($data, $options) {
    }

     // 更新成功后的回调方法
    protected function _after_update($data, $options) {

    }
    // 删除数据前的回调方法
    protected function _before_delete($options) {
        $is_mine = $this->where($options['where'])->getField('is_mine');
        if($is_mine==0){
            jsonError('内置报表不可删除');
        }

        //清除归属文件
        $this->_deletefile($options['where']);
    }

    // 删除成功后的回调方法
    protected function _after_delete($data, $options) {
        //  echo __METHOD__,'<br/>';
        // var_dump($data);
        //  var_dump($options);

    }

     // 查询成功后的回调方法
    protected function _after_select(&$resultSet, $options) {
        //查询是否带有content结果集，如果有那么直接将结果集由json转为array
        foreach ($resultSet as &$a) {
            if(isset($a['content'])){
                $a['content'] = json_decode($a['content'],true);
            }else{
                break;
            }
        }
    }

   /**
    *  清除归属文件
    */
   private function _deletefile($where){
        //清除文件和图标
        $item = $this->field('content,thumb')->where($where)->find();
        if(!empty($item)){
            @unlink($item['thumb']);
            @unlink(json_decode($item['content'],true)['addForm']['bgImgUrl']);            
        }
   }
   /**
    * 聚合参数列表
    */
    public static function aggparams(){
        $result = [
            ['name'=>'设备ip','value'=>'device'],
            ['name'=>'来源ip','value'=>'src_ip'],
            ['name'=>'目标ip','value'=>'dst_ip'],
            ['name'=>'来源端口','value'=>'src_port'],
            ['name'=>'目标端口','value'=>'dst_port'],
            ['name'=>'电厂名称','value'=>'facname'],
            ['name'=>'电厂简称','value'=>'nickname'],
            ['name'=>'采集器区域','value'=>'sentype'],
            ['name'=>'告警名称','value'=>'alarm_name'],
            ['name'=>'告警种类','value'=>'alarm_type'],
            ['name'=>'告警级别','value'=>'level'],
            ['name'=>'网口','value'=>'interface'],
            ['name'=>'优先级','value'=>'priority'],
            ['name'=>'协议','value'=>'protocol'],
            ['name'=>'上传事件等级名称','value'=>'uploadLogLevelName'],
            ['name'=>'上传事件等级','value'=>'uploadLogLevel'],
            ['name'=>'插件ID','value'=>'plugin_id'],
            ['name'=>'插件子ID','value'=>'plugin_sid'],
            ['name'=>'时间','value'=>'timestamp'],
        ];
        return $result;
    }
    /**
    * 电厂实施趋势统计--获取7天数据原料
    */
    public function isp7day(){
        //取出时间
        $day6 = $this->table('factory_online_state')->where([
            'create_time' => array('like',"%".date("Y-m-d",strtotime("-6 day"))."%")
        ])->order('id desc')->find();
        if(\Tools::isEmpty($day6)){
            $day6 = ['online_num'=>0,'offline_num'=>0,'total_num'=>0];
        }
        $day5 = $this->table('factory_online_state')->where([
            'create_time' => array('like',"%".date("Y-m-d",strtotime("-5 day"))."%")
        ])->order('id desc')->find();
        if(\Tools::isEmpty($day5)){
            $day5 = ['online_num'=>0,'offline_num'=>0,'total_num'=>0];
        }

        $day4 = $this->table('factory_online_state')->where([
            'create_time' => array('like',"%".date("Y-m-d",strtotime("-4 day"))."%")
        ])->order('id desc')->find();
        if(\Tools::isEmpty($day4)){
           $day4 = ['online_num'=>0,'offline_num'=>0,'total_num'=>0];
        }

        $day3 = $this->table('factory_online_state')->where([
            'create_time' => array('like',"%".date("Y-m-d",strtotime("-3 day"))."%")
        ])->order('id desc')->find();
        if(\Tools::isEmpty($day3)){
            $day3 = ['online_num'=>0,'offline_num'=>0,'total_num'=>0];
        }

        $day2 = $this->table('factory_online_state')->where([
            'create_time' => array('like',"%".date("Y-m-d",strtotime("-2 day"))."%")
        ])->order('id desc')->find();
        if(\Tools::isEmpty($day2)){
            $day2 = ['online_num'=>0,'offline_num'=>0,'total_num'=>0];
        }

        $day1 = $this->table('factory_online_state')->where([
            'create_time' => array('like',"%".date("Y-m-d",strtotime("-1 day"))."%")
        ])->order('id desc')->find();
        if(\Tools::isEmpty($day1)){
            $day1 = ['online_num'=>0,'offline_num'=>0,'total_num'=>0];
        }

        $day0 = $this->table('factory_online_state')->where([
            'create_time' => array('like',"%".date("Y-m-d",strtotime("-1 day"))."%")
        ])->order('id desc')->find();
        if(\Tools::isEmpty($day0)){
            $day0 = ['online_num'=>0,'offline_num'=>0,'total_num'=>0];
        }

        //初始化数据原料
        $data = [
            'isp' => [
                'name' => '运营商',
                'data' =>[
                    [
                        'yd' => 0,
                        'lt' => 0,
                        'dx' => 0,
                        'value' => 0,
                        'time' => date("Y-m-d",strtotime("-6 day")),
                    ],
                    [
                        'yd' => 0,
                        'lt' => 0,
                        'dx' => 0,
                        'value' => 0,
                        'time' => date("Y-m-d",strtotime("-5 day")),
                    ],
                    [
                        'yd' => 0,
                        'lt' => 0,
                        'dx' => 0,
                        'value' => 0,
                        'time' => date("Y-m-d",strtotime("-4 day")),
                    ],
                    [
                        'yd' => 0,
                        'lt' => 0,
                        'dx' => 0,
                        'value' => 0,
                        'time' => date("Y-m-d",strtotime("-3 day")),
                    ],
                    [
                        'yd' => 0,
                        'lt' => 0,
                        'dx' => 0,
                        'value' => 0,
                        'time' => date("Y-m-d",strtotime("-2 day")),
                    ],
                    [
                        'yd' => 0,
                        'lt' => 0,
                        'dx' => 0,
                        'value' => 0,
                        'time' => date("Y-m-d",strtotime("-1 day")),
                    ],
                    [
                        'yd' => 0,
                        'lt' => 0,
                        'dx' => 0,
                        'value' => 0,
                        'time' => date("Y-m-d"),
                    ]
                ]
            ],
            'online' => [
                'name' => '在线数量',
                'data' => [               
                    [
                        'online' => (int)$day6['online_num'],
                        'offline' => (int)$day6['offline_num'],
                        'value' => (int)$day6['online_num'],
                        'total' => (int)$day6['total_num'],
                        'time' => date("Y-m-d",strtotime("-6 day")),
                    ],
                    [
                        'online' => (int)$day5['online_num'],
                        'offline' => (int)$day5['offline_num'],
                        'value' => (int)$day5['online_num'],
                        'total' => (int)$day5['total_num'],
                        'time' => date("Y-m-d",strtotime("-5 day")),
                    ],
                    [
                        'online' =>(int)$day4['online_num'],
                        'offline' =>(int)$day4['offline_num'],
                        'value' => (int)$day4['online_num'],
                        'total' => (int)$day4['total_num'],
                        'time' => date("Y-m-d",strtotime("-4 day")),
                    ],
                    [
                        'online' => (int)$day3['online_num'],
                        'offline' =>(int)$day3['offline_num'],
                        'value' => (int)$day3['online_num'],
                        'total' => (int)$day3['total_num'],
                        'time' => date("Y-m-d",strtotime("-3 day")),
                    ],
                    [
                        'online' => (int)$day2['online_num'],
                        'offline' =>(int)$day2['offline_num'],
                        'value' => (int)$day2['online_num'],
                        'total' => (int)$day2['total_num'],
                        'time' => date("Y-m-d",strtotime("-2 day")),

                    ],
                    [
                        'online' => (int)$day1['online_num'],
                        'offline' =>(int)$day1['offline_num'],
                        'value' => (int)$day1['online_num'],
                        'total' => (int)$day1['total_num'],
                        'time' => date("Y-m-d",strtotime("-1 day")),
                    ],
                    [
                        'online' => (int)$day0['online_num'],
                        'offline' =>(int)$day0['offline_num'],
                        'value' => (int)$day0['online_num'],
                        'total' => (int)$day0['total_num'],
                        'time' => date("Y-m-d"),
                    ]
                ]
            ]
        ];
        return $data;
    }

    /**
    * 公司视角--电厂类型统计
    * @param $input 电厂ID
    */
    public function factypesum($input){
        //电厂初始化数据
        $result = $this->table('factory_type')->field('type_name factype,type_banner banner, 0 abnormal_num,0 normal_num')->select();

        //匹配条件
        $solt = $this->table('factory')->field('factype type_name,count(facid) num,`status`')->where($input)->group('type_name,`status`')->select();

        foreach($result as &$a) {
            foreach ($solt as $b){
                if($b['type_name']==$a['factype']){
                    switch ($b['status']) {
                        case '0'://异常
                            $a['abnormal_num'] = $b['num'];
                            break;
                        case '1'://正常
                            $a['normal_num'] = $b['num'];
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
        }
        return self::iconvInt($result);
    }

    /**
    * 公司视角--VPDN--运营商统计
    * @param $input 电厂ID
    */
    public function vpdnIsp($input){
        $where['powerPlant'] = ['in',array_values(array_column($this->table('factory')->field('facname')->where($input)->select(),'facname'))];
        
    }

    //将可以转换的值都转换为int
    private function iconvInt($input){
        array_walk_recursive($input,function(&$val){
            //如果不是，就将数字元素转换为数字类型
            $val = is_numeric($val) ? (int)$val : $val;
        });
        return $input;
    }

    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
    public function getModelName() {
        return '报表管理';
    }     
}
