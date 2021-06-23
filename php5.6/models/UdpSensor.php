<?php
namespace App\Models;

class UdpSensor extends Model{
    protected $tableName = 'sensor';
    protected $tablePrefix = 'udp_';
    protected $pk = 'ip';

    //探针路径
    static private $agent_src = 'python /work/sensor_manager/agent/';
    //错误信息
    public $errors = ''; 

    /** 定义验证逻辑 */
    protected $_validate = array(
        array('name', 'require', '设备名称不能为空'),
        array('name','','设备已经存在',0,'unique'),//验证唯一性
        array('ip','filter_var','非IP地址',1,'function',3,array(FILTER_VALIDATE_IP)),
        array('ip','','IP已经存在',0,'unique'),//验证唯一性
    );

    public function getList($where = [],$page,$pagesize,$order='a.ip asc')
    {

        //$result['config'] = $this->getComment();

        $result['list'] =  $this->alias('a')->where($where)->order($order)->page($page,$pagesize)->select();

        /*
         获得附加属性
        */
         if(!\Tools::isEmpty($result['list'])){
            array_walk($result['list'],function(&$arr){
                $arr['subtype'] = $this->getSubType($arr['subtype']);
                //标识平台或者采集器
                if(stripos($arr['subtype'],'采集器')){
                    $arr['type'] = '采集器';
                }
                
                if(stripos($arr['subtype'],'平台')){
                    $arr['type'] = '平台';
                }
            });
         }


        $result['total_num'] = $this->alias('a')->where($where)->count();

        return $result;
    
    }

    /**
    * 获取单个
    */

    public function getOne($where = [])
    {
        $result = $this->where($where)->find();
        if(!\Tools::isEmpty($result)){
            $result['type'] = [
                $result['type'],$result['subtype']
            ];
        }
        
        return $result;
    }

    /**
    * 统计设备类型数量(主备平台和采集器)
    */
    public function platAndSenosrCount()
    {
        $where = [
            'b.name' => ['like',['%采集器','%平台'],'OR'],
        ];
        $result = $this->field('b.name,count(a.ip) `value`,a.online_status,collect_status,traffic_status,switch_status,agent_status,backup_status')->alias('a')->join('device_types b on a.subtype=b.id','LEFT')->group('a.subtype')->where($where)->select();
        //将数据重新组装
        if(!\Tools::isEmpty($result)){
           array_walk($result,function(&$arr){
            $detail = [
                'online_status' => $arr['online_status'],
                'collect_status' => $arr['collect_status'],
                'traffic_status' => $arr['traffic_status'],
                'switch_status' => $arr['switch_status'],
                'agent_status' => $arr['agent_status'],
                'backup_status' => $arr['backup_status'],
            ];
            unset($arr['online_status'],$arr['collect_status'],$arr['traffic_status'],$arr['switch_status'],$arr['online_status'],$arr['agent_status'],$arr['backup_status']);
            $arr['detail'] = $detail;

            }); 
        }
        
        return $result;
    }
    /**
    * 拥有的所有类型
    */
    public function getTypes()
    {
        $where = [
            'a.name' => ['like',['%采集器','%平台'],'OR'],
        ];
        $result = $this->table('device_types a')->field('id,name')->where($where)->select();
        return $result;
    }
    /**
    * 获得采集器归属分类
    * $subtype 所属子分类
    */
    private function getSubType($subtype)
    {
        $where = [
            'a.id' => $subtype,
        ];
        $result = $this->table('device_types a')->where($where)->getField('name');
        return $result;
    }

    // 插入数据前的回调方法
    protected function _before_insert(&$data, $options) {

    }

    // 插入成功后的回调方法
    protected function _after_insert($data, $options) {

    }
    // 更新数据前的回调方法
    protected function _before_update(&$data, $options) {

    }

    // 更新成功后的回调方法
    protected function _after_update($data, $options) {
        
    }
    // 删除数据前的回调方法
    protected function _before_delete($options) {

    }
    // 删除成功后的回调方法
    protected function _after_delete($data, $options) {

    }
    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
    public function getModelName() {
        return '设备管理';
    }
    /**
    * 获取全部探针
    */
    public function allAgent(){
        $where = [
            'a.name' => ['like','%采集器'],
        ];
        $temp = $this->table('device_types a')->field('id,name')->where($where)->select();
        $subtype = array_column($temp,'id');
        
        $where = [
            'subtype' => ['in',$subtype]
        ];
  
        $ret = $this->field('ip,name,port,ver')->where($where)->order('ctime desc')->select();

        /*
         获得资产附加属性
        */
        if (!\Tools::isEmpty($ret)) {
            array_walk($ret, function (&$arr) {

                $status =  \IpLocation::nc_port($arr['ip'],$arr['port']);
                $arr['status'] = stripos($status,'ok') > -1 ? true : false;
            });
        }


        return $ret;
    }

    /***
    * 调阅查询
    */
    public function browse($where){
        try{

            //网络情况
            static::nc_port($where);

            $command = static::$agent_src."collect_config.py -t {$where['ip']} -p {$where['port']} --para {$where['para']}";

            exec($command,$res,$status);

            $ret = [];

            if($status==0){
                
                //前置处理
                foreach($res as $v){
                    if(strstr($v,'result :')){
                        break;
                    }else{
                        $msg.=array_shift($res);
                    }
                }

                $res = json_decode(ltrim(implode('',$res),"result :"),true);
                if(!\Tools::isEmpty($res[0])){
                    $ret = array_column($res,key($res[0]));
                }

            }

            return $ret;

        }catch(\Exception $e){
            return $e->getMessage();
        } 
    }
    /**
    * 通过cli模式执行脚本命令
    * @params $command String 当前命令
    * @params @async Bool 是否异步
    */
    private static function execConfig($command,$async=false){
        exec($command,$res,$status);

        //exit($command);
        if($status==0){
            
            //是否异步，是的话直接返回成功
            if($async){
                return true;   
            }else{

                $temp = static::removeSeniment($res);

                if(empty($temp)){
                    $this->errors = implode('',$res).",command:{$command}";
                    return False;
                }

                $ind = stripos($temp,'Successed');
                if($ind === false){
                    $ind = stripos($temp,'成功');
                }
                return $ind;
            }    

        }else{
            return False;
        }
    }


    /**
    * 去除cli模式的杂质数据，保留有效信息
    */
    private static function removeSeniment($res){
         //前置处理
        foreach($res as $v){
            if(strstr($v,'result :')){
                break;
            }else{
                if(strstr($v,'参数')){
                    $msg = '';
                    break;
                }else{
                    $msg.=array_shift($res);
                } 
            }
        }

        return trim($msg);
    }

    /**
    * 检测网络状态
    */
    private static function nc_port($where){
        $status =  \IpLocation::nc_port($where['ip'],$where['port']);
        if($status == 'no'){
            jsonError('设备离线');
        }
    }

     /**
    * 配置资产
    */
    public function configAsset($params){
        try{

            //网络情况
            //static::nc_port($params);

            //数据效验
            if($params['act']=='add') $params['act']=2;
            if($params['act']=='update') $params['act']=3;
            if($params['act']=='del') $params['act']=4;

            $command = static::$agent_src."config_assert.py -t {$params['ip']} -p {$params['port']} --cmdpara {$params['act']}";

            if(!empty($params['id'])) $command.=" --id={$params['id']}";
            if(!empty($params['assert_name'])) $command.=' --assert_name="'.$params['assert_name'].'"';
            if(!empty($params['ipaddra'])) $command.=" --ipaddra={$params['ipaddra']}";
            if(!empty($params['maca'])) $command.=" --maca={$params['maca']}";
            if(!empty($params['seller'])) $command.=" --seller={$params['seller']}";
            if(!empty($params['ipaddrb'])) $command.=" --ipaddrb={$params['ipaddrb']}";
            if(!empty($params['macb'])) $command.=" --macb={$params['macb']}";
            if(!empty($params['devtype'])) $command.=" --devtype={$params['devtype']}";
            if(!empty($params['csn'])) $command.=" --csn={$params['csn']}";
            if(!empty($params['ostype'])) $command.=" --ostype={$params['ostype']}";
            if(!empty($params['snmptype'])) $command.=" --snmptype={$params['snmptype']}";
            if(!empty($params['username'])) $command.=" --username={$params['username']}";
            if(!empty($params['authtype'])) $command.=" --authtype={$params['authtype']}";
            if(!empty($params['encrypttype'])) $command.=" --encrypttype={$params['encrypttype']}";
            if(!empty($params['snmpread'])) $command.=" --snmpread={$params['snmpread']}";
            if(!empty($params['snmpwrite'])) $command.=" --snmpwrite={$params['snmpwrite']}";

            //数据执行
            //exit($command);

            return static::execConfig($command);     


        }catch(\Exception $e){
            return $e->getMessage();
        } 
    }

    /**
    * 网关配置
    */
    public function configNetcard($params){
        try{

            //网络情况
            //static::nc_port($params);

            //数据效验
            if($params['act']=='add') $params['act']=2;
            if($params['act']=='update') $params['act']=3;
            if($params['act']=='del') $params['act']=4;

            $command = static::$agent_src."config_netcard.py -t {$params['ip']} -p {$params['port']} --cmdpara {$params['act']}";

            if(!empty($params['id'])) $command.=" --id={$params['id']}";
            if(!empty($params['netcard_name'])) $command.=' --name="'.$params['netcard_name'].'"';
            if(!empty($params['ipaddr'])) $command.=" --ipaddr={$params['ipaddr']}";
            if(!empty($params['submask'])) $command.=" --submask={$params['submask']}";
            //因为有可能参数值为0，所以不做判断
            //$command.=" --linkstatus={$params['linkstatus']}";
            //不可修改项目
            $command.=" --enablestatus={$params['enablestatus']}";
            $command.=" --idsport={$params['idsport']}";
            //数据执行
            //exit($command);

            return static::execConfig($command);


        }catch(\Exception $e){
            $this->errors = $e->getMessage();
            return false;
        } 
    }

    /**
    * 路由配置
    */
    public function configRouter($params){
        try{

            //网络情况
            //static::nc_port($params);

            //数据效验
            if($params['act']=='add') $params['act']=2;
            if($params['act']=='update') $params['act']=3;
            if($params['act']=='del') $params['act']=4;

            $command = static::$agent_src."config_router.py -t {$params['ip']} -p {$params['port']} --cmdpara {$params['act']}";

            if(!empty($params['id'])) $command.=" --id={$params['id']}";
            if(!empty($params['gateway'])) $command.=' --gateway='.$params['gateway'];
            if(!empty($params['ipaddr'])) $command.=" --ipaddr={$params['ipaddr']}";
            if(!empty($params['submask'])) $command.=" --submask={$params['submask']}";

            //数据执行
            //exit($command);

            return static::execConfig($command);


        }catch(\Exception $e){
            return $e->getMessage();
        } 
    }

    /**
    * 系统信息配置
    */
    public function configSysInfo($params){
        try{

            //网络情况
            //static::nc_port($params);


            $command = static::$agent_src."config_sysinfo.py -t {$params['ip']} -p {$params['port']}";

            if(!empty($params['softver'])) $command.=' --softver="'.$params['softver'].'"';
            if(!empty($params['sysip'])) $command.=' --sysip="'.$params['sysip'].'"';
            if(!empty($params['devmac'])) $command.=' --devmac="'.$params['devmac'].'"';
            if(!empty($params['devname'])) $command.=' --devname="'.$params['devname'].'"';//非中文，否则获取列表出现错误
            if(!empty($params['eventdevname'])) $command.=' --eventdevname="'.$params['eventdevname'].'"';
            if(!empty($params['vendor'])) $command.=' --vendor="'.$params['vendor'].'"';

            //数据执行
            //exit($command);

            return static::execConfig($command);


        }catch(\Exception $e){
            return $e->getMessage();
        } 
    }

     /**
    * 基础信息配置
    */
    public function configSysLog($params){
        try{

            //网络情况
            //static::nc_port($params);


            $command = static::$agent_src."config_syslog.py -t {$params['ip']} -p {$params['port']}";

            if(!\Tools::isEmpty($params['state'])) $command.=" --state={$params['state']}";
            if(!\Tools::isEmpty($params['devip'])) $command.=" --ip={$params['devip']}";
            if(!\Tools::isEmpty($params['logport'])) $command.=" --logport={$params['logport']}";
            if(!\Tools::isEmpty($params['ntpmode'])) $command.=" --ntpmode={$params['ntpmode']}";//非中文，否则获取列表出现错误
            if(!\Tools::isEmpty($params['beepmode'])) $command.=" --beepmode={$params['beepmode']}";
            if(!\Tools::isEmpty($params['connmode'])) $command.=" --connmode={$params['connmode']}";
            if(!\Tools::isEmpty($params['pcap_state'])) $command.=" --pcapstate={$params['pcap_state']}";
            if(!\Tools::isEmpty($params['pcap_ip'])) $command.=" --pcapip={$params['pcap_ip']}";
            if(!\Tools::isEmpty($params['pcap_port'])) $command.=" --pcapport={$params['pcap_port']}";
            if(!\Tools::isEmpty($params['tp_state'])) $command.=" --tp_state={$params['tp_state']}";
            if(!\Tools::isEmpty($params['tp_eth'])) $command.=" --tp_eth={$params['tp_eth']}";

            //数据执行
            //exit($command);

            return static::execConfig($command);


        }catch(\Exception $e){
            return $e->getMessage();
        } 
    }
    /**
    * 获取全部设备类型
    */

    public function allDeviceType(){
        return [
            ['value'=>'FW','name'=>'防火墙'],
            ['value'=>'FID','name'=>'横向正向隔离装置'],
            ['value'=>'BID','name'=>'横向反向隔离装置'],
            ['value'=>'SVR','name'=>'服务器'],
            ['value'=>'SW','name'=>'交换机'],
            ['value'=>'VEAD','name'=>'纵向加密装置'],
            ['value'=>'AV','name'=>'防病毒系统'],
            ['value'=>'IDS','name'=>'入侵检测系统'],
            ['value'=>'BD','name'=>'数据库'],
            ['value'=>'DCD','name'=>'网络安全检测装置'],       
        ];
    }
    /**
    * 升级
    */
    public function configUpgrade($ips){

        $status = array();

        //是否异步
        $async=true;
        
        //日志地址
        $async_log=APP_PATH.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'upgrade_'.date('Y-m-d').'.log';

        # 操作模式
        $add = '> ';
        $append = '>> ';
        $background = '&';

        if(is_string($ips)){
            $command = static::$agent_src."config_upgrade.py {$ips} {$add} {$async_log} {$background}";
            return static::execConfig($command,$async_log);
        }
        if(is_array($ips)){
            foreach ($ips as $k => $item){
                if($k==0){
                    $command = static::$agent_src."config_upgrade.py {$item} {$add} {$async_log} {$background}";
                }else{
                    $command = static::$agent_src."config_upgrade.py {$item} {$append} {$async_log} {$background}";
                }

                $status[] = static::execConfig($command,$async);

            }

            return $status;
        }



         
        return $status;
        

    }

     /**
    * 获取升级日志
    */

    public function upgradeLogger(){

        //日志地址
        $async_log=APP_PATH.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'upgrade_'.date('Y-m-d').'.log';

        if(file_exists($async_log))
        {
            return file_get_contents($async_log);
        }else{
            return false;
        }
    }
    /***
    * 调阅查询
    */
    public function device($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];

        $host = $this->field('host_id,name,ip,contact,descr,type,subtype,port')->where($whe)->find();
        if (\Tools::isEmpty($host)) jsonError('采集器关联信息有误');

        //cli模式获取参数
        $command = static::$agent_src."collect_config.py -t {$whe['ip']} -p {$whe['port']} --type 15 --para 0 --cmdpara 0";
        exec($command,$res,$status);

        if($status==0){
            
            //前置处理
            foreach($res as $v){
                if(strstr($v,'result :')){
                    break;
                }else{
                    $msg.=array_shift($res);
                }
            }

            $res = json_decode(ltrim(implode('',$res),"result :"),true);

            $host['net'] = $res['flow'];//网络流量
            $host['model'] = $res['model'];//设备类型
            $host['devid'] = $res['devID'];//设备型号
            $host['ver'] = $res['version'];//版本
            $host['cpu'] = $res['cpu'];//cpu
            $host['mem'] = $res['memory'];//内存
            $host['disk'] = $res['storage'];//硬盘
            $host['status'] = $res['status'];//状态
        }



        unset($whe);
        $whe['id'] = ['exp','=unhex("'.$host['host_id'].'")'];
        $attributes = $this->table('host')->field('asset,country')->where($whe)->find(); 
        if (\Tools::isEmpty($attributes)) jsonError('采集器关联信息有误');





        return array_merge($host,$attributes);
        
    }
    /***
    * 保存基础信息
    */
    public function saveDevice($where,$data){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];

        $host = $this->where($whe)->find(); 
        if (\Tools::isEmpty($host)) jsonError('采集器不存在');

        $this->where($whe)->save($data);
        return true;
        
    }
    /***
    * 白名单列表
    */
    public function WhiteList($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];

        //cli模式获取参数
        $command = static::$agent_src."config_monitor.py -t {$whe['ip']} -p {$whe['port']} --subtype 1 --cmdpara 1 --ident 123456789123465 --ip 127.0.0.1";
        exec($command,$res,$status);

        $ret = [];

        if($status==0){

            $file = '/work/sensor_manager/agent/return/monitor/0x01';

            if(file_exists($file) and is_readable($file) and is_writeable($file)){
                $hd = file($file);
                if(!empty($hd)){
                    for($i=0;$i<sizeof($hd);$i++){
                        $ret[] = static::formatListItem($hd[$i],($i+1));
                    }                   
                }
            }
            
            return $ret;
        }else{

            $this->errors = implode('',$res);
            return False;
        }    
    }

    /**
    * 数据源整理
    * $string 字符串
    * $num 行号
    */
    private static function formatListItem($string,$num){
        $string = rtrim($string,"\n");
        $result = explode(',',$string);
        array_unshift($result,$num);
        $keys = ['id','proto','address','number'];

        return array_combine($keys,$result);
    }

    /***
    * 保存白名单
    */
    public function saveWhite($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];

        //cli模式获取参数
        $command = static::$agent_src."config_monitor.py -t {$whe['ip']} -p {$whe['port']} --subtype 1 --cmdpara 1 --ident 123456789123465 --ip 127.0.0.1";
        exec($command,$res,$status);


        if($status==0){
            $file = '/work/sensor_manager/agent/return/monitor/0x01';
            if(file_exists($file) and is_readable($file) and is_writeable($file)){
                if($where['id'] == 0){
                    $item = "{$where['proto']},{$where['address']},{$where['number']}";
                    $command2 = "test -s {$file} && sed -i '\$a {$item}' {$file} || echo '{$item}' >> {$file}";
                    `{$command2}`;
                }else{
                    $item = "{$where['proto']},{$where['address']},{$where['number']}";
                    $command2 = "sed -i '{$where['id']}c {$item}' {$file}";
                    `{$command2}`;
                }
                $command = static::$agent_src."config_monitor.py -t {$whe['ip']} -p {$whe['port']} --subtype 1 --cmdpara 2 --ident 123456789123465 --ip 127.0.0.1";
                $ret = `{$command}`;
                return true;
            }
            return False;
        }else{
            $this->errors = implode('',$res);
            return False;
        }    
    }    
    /**
    * 规则列表--白名单-删除
    */
    public function destroyWhite($where){
        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];

        //cli模式获取参数
        $command = static::$agent_src."config_monitor.py -t {$whe['ip']} -p {$whe['port']} --subtype 1 --cmdpara 1 --ident 123456789123465 --ip 127.0.0.1";
        exec($command,$res,$status);
        if($status==0){
            $file = '/work/sensor_manager/agent/return/monitor/0x01';
            if(file_exists($file) and is_readable($file) and is_writeable($file)){
                `sed -i '{$where['id']}d' {$file}`;

                $command = static::$agent_src."config_monitor.py -t {$whe['ip']} -p {$whe['port']} --subtype 1 --cmdpara 2 --ident 123456789123465 --ip 127.0.0.1";
                $ret = `{$command}`;
                return true;
            }
            return False;
        }else{
            $this->errors = implode('',$res);
            return False;
        } 
    }
    
}