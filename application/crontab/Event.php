<?php
include_once '/work/web/vendor/autoload.php';


use GeoIp2\Database\Reader;

error_reporting(E_ALL &~E_NOTICE &~E_WARNING); 
/**
* 安全事件Event定时任务
*/
class Crontab_event{

	/* 代表采集器组 */
	private $sensor_type = '1101,1102,1103,1201,1202,1203';
	private $pdo;
	private $country;
	private $_REDIS;
	//前缀
	public $redis_prefix = 'sensor-ip-';

	public function __construct(){
		$this->pdo = new PDO('mysql:host=127.0.0.1;dbname=alienvault;charset=utf8','root','123456');
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);//设置筛选模式

		$this->country = new Reader(dirname(__FILE__,2).'/library/GeoLite2-Country.mmdb'); 
		$this->redis();
	}

	/**
	* 连接redis句柄
	*/
	public function redis(){
		if(extension_loaded('redis')){
			$this->_REDIS= new \Redis();
			$this->_REDIS->connect('127.0.0.1',6379);
			return $this->_REDIS; 
		}else{
			exit('redis is not found.');
		}
	}

	public function set($key,$value,$type=0,$repeat=0,$time=0,$old=0)  
    {  
        $return = null;  
  
        if ($type) {  
            $return = $this->_REDIS->append($key, $value);  
        } else {  
            if ($old) {  
                $return = $this->_REDIS->getSet($key, $value);  
            } else {  
                if ($repeat) {  
                    $return = $this->_REDIS->setnx($key, $value);  
                } else {  
                    if ($time && is_numeric($time)) $return = $this->_REDIS->setex($key, $time, $value);  
                    else $return = $this->_REDIS->set($key, $value);  
                }  
            }  
        }  
  
        return $return;  
    }  
  
    //+++-------------------------哈希操作-------------------------+++//  
  
    /** 
     * 将key->value写入hash表中 
     * @param $hash string 哈希表名 
     * @param $data array 要写入的数据 array('key'=>'value') 
     */  
    public function hashSet($hash,$data)  
    {  
        $return = null;  
  
        if (is_array($data) && !empty($data)) {  
            $return = $this->_REDIS->hMset($hash, $data);  
        }  
  
        return $return;  
    }  
  
    /** 
     * 获取hash表的数据 
     * @param $hash string 哈希表名 
     * @param $key mixed 表中要存储的key名 默认为null 返回所有key>value 
     * @param $type int 要获取的数据类型 0:返回所有key 1:返回所有value 2:返回所有key->value 
     */  
    public function hashGet($hash,$key=array(),$type=0)  
    {  
        $return = null;  
  
        if ($key) {  
            if (is_array($key) && !empty($key))  
                $return = $this->_REDIS->hMGet($hash,$key);  
            else  
                $return = $this->_REDIS->hGet($hash,$key);  
        } else {  
            switch ($type) {  
                case 0:  
                    $return = $this->_REDIS->hKeys($hash);  
                break;  
                case 1:  
                    $return = $this->_REDIS->hVals($hash);  
                break;  
                case 2:  
                    $return = $this->_REDIS->hGetAll($hash);  
                break;  
                default:  
                    $return = false;  
                break;  
            }  
        }  
  
        return $return;  
    }    
    /** 
     * 获取hash表中元素个数 
     * @param $hash string 哈希表名 
     */  
    public function hashLen($hash)  
    {  
        $return = null;  
  
        $return = $this->_REDIS->hLen($hash);  
  
        return $return;  
    }  
  
    /** 
     * 删除hash表中的key 
     * @param $hash string 哈希表名 
     * @param $key mixed 表中存储的key名 
     */  
    public function hashDel($hash,$key)  
    {  
        $return = null;  
  
        $return = $this->_REDIS->hDel($hash,$key);  
  
        return $return;  
    }  
  
    /** 
     * 查询hash表中某个key是否存在 
     * @param $hash string 哈希表名 
     * @param $key mixed 表中存储的key名 
     */  
    public function hashExists($hash,$key)  
    {  
        $return = null;  
  
        $return = $this->_REDIS->hExists($hash,$key);  
  
        return $return;  
    }
    /** 
     * 自增hash表中某个key的值 
     * @param $key string 哈希表名 
     * @param $time mixed 表中存储的key名 
     */  
    public function setKeyExpire($key, $time)  
    {  
        $return = null;  
  
        $return = $this->_REDIS->setTimeout($key, $time);  
  
        return $return;  
    } 

	/**
	* 查询字符串
	*/
	public function query($sql){
		$statement = $this->pdo->prepare($sql);
		$statement->execute();
		return $statement->fetchAll();
	}
	/**
	* 查询字符串
	*/
	public function find($sql){
		$statement = $this->pdo->prepare($sql);
		$statement->execute();
		return $statement->fetch();
	}

	/**
	* 连接数据库句柄
	*/
	public function sensor(){

		$ret = array();

		$sensor = $this->query('select a.name,a.ip,b.id,hex(b.sensor_id) as sensor_id from alienvault.udp_sensor a inner join alienvault_siem.device b on a.ip = inet6_ntoa(b.device_ip) where a.subtype in('.$this->sensor_type.') ');


		foreach($sensor as $a){
			$acid_event = $this->query('SELECT hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net FROM alienvault_siem.acid_event a where a.device_id='.$a['id'].' ORDER BY a.TIMESTAMP DESC LIMIT 0,1000
			');

			foreach ($acid_event as $k => &$v){
				
				if (filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
	                $v['src_flag'] = '';
	                $v['src_flag_name'] = '';
	            } else {
	                $v['src_flag'] = strtolower($this->getisoCode($v['src_ip']));
	                $v['src_flag_name'] = $this->getIpCountryName($v['src_ip']);
	            }

	            if (filter_var($v['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
	                $v['dst_flag'] = '';
	                $v['dst_flag_name'] = '';
	            } else {
	                $v['dst_flag'] = strtolower($this->getisoCode($v['dst_ip']));
	                $v['dst_flag_name'] = $this->getIpCountryName($v['dst_ip']);
	            }

	            if(!empty($v['src_mac'])){
	                $v['src_mac'] = substr($v['src_mac'], 0, 2) . '-' . substr($v['src_mac'], 2, 2) . '-' . substr($v['src_mac'], 4, 2) . '-' . substr($v['src_mac'], 6, 2) . '-' . substr($v['src_mac'], 8, 2) . '-' . substr($v['src_mac'], 10, 2);
	            }

	            if(!empty($v['dst_mac'])){
	                $v['dst_mac'] = substr($v['dst_mac'], 0, 2) . '-' . substr($v['dst_mac'], 2, 2) . '-' . substr($v['dst_mac'], 4, 2) . '-' . substr($v['dst_mac'], 6, 2) . '-' . substr($v['dst_mac'], 8, 2) . '-' . substr($v['dst_mac'], 10, 2);
	            }

	            $v['eventname'] = $this->getEventName($v['plugin_id'].':'.$v['plugin_sid']);
	            $v['protocol'] = $this->getPro($v['protocol']);
	            $v['device_ip'] = $a['ip'];
	            $v['device_hostname'] = is_null($a['name'])?'未设置':$a['name'];

	            $v['src_hostname'] = $this->getHostName($v['src_ip'])?$this->getHostName($v['src_ip']):'未设置';
	            $v['dst_hostname'] = $this->getHostName($v['dst_ip'])?$this->getHostName($v['dst_ip']):'未设置';
	            $v['agent_ctx'] = $v['agent_ctx']?$v['agent_ctx']:'未设置';
	            $v['sensor_id'] = $v['sensor_id']?$v['sensor_id']:'未设置';
	            $v['src_host'] = $v['src_host']?$v['src_host']:'未设置';
	            $v['src_mac'] = $v['src_mac']?$v['src_mac']:'未设置';
	            $v['src_net'] = $v['src_net']?$v['src_net']:'未设置';
	            $v['dst_host'] = $v['dst_host']?$v['dst_host']:'未设置';
	            $v['dst_mac'] = $v['dst_mac']?$v['dst_mac']:'未设置';
	            $v['dst_net'] = $v['dst_net']?$v['dst_net']:'未设置';

	            $v['sensor_id'] = $a['sensor_id'];


			}


			$ret[$a['ip']] = $acid_event;
		}

		return $ret;
	}

	/**
	* 管理员
	*/
	public function admin(){

			$acid_event = $this->query('SELECT hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net FROM alienvault_siem.acid_event a ORDER BY a.TIMESTAMP DESC LIMIT 0,1000
			');

			foreach ($acid_event as $k => &$v){
				
				$a = $this->getDevice($v['device_id']);

				if (filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
	                $v['src_flag'] = '';
	                $v['src_flag_name'] = '';
	            } else {
	                $v['src_flag'] = strtolower($this->getisoCode($v['src_ip']));
	                $v['src_flag_name'] = $this->getIpCountryName($v['src_ip']);
	            }

	            if (filter_var($v['dst_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
	                $v['dst_flag'] = '';
	                $v['dst_flag_name'] = '';
	            } else {
	                $v['dst_flag'] = strtolower($this->getisoCode($v['dst_ip']));
	                $v['dst_flag_name'] = $this->getIpCountryName($v['dst_ip']);
	            }

	            if(!empty($v['src_mac'])){
	                $v['src_mac'] = substr($v['src_mac'], 0, 2) . '-' . substr($v['src_mac'], 2, 2) . '-' . substr($v['src_mac'], 4, 2) . '-' . substr($v['src_mac'], 6, 2) . '-' . substr($v['src_mac'], 8, 2) . '-' . substr($v['src_mac'], 10, 2);
	            }

	            if(!empty($v['dst_mac'])){
	                $v['dst_mac'] = substr($v['dst_mac'], 0, 2) . '-' . substr($v['dst_mac'], 2, 2) . '-' . substr($v['dst_mac'], 4, 2) . '-' . substr($v['dst_mac'], 6, 2) . '-' . substr($v['dst_mac'], 8, 2) . '-' . substr($v['dst_mac'], 10, 2);
	            }

	            $v['eventname'] = $this->getEventName($v['plugin_id'].':'.$v['plugin_sid']);
	            $v['protocol'] = $this->getPro($v['protocol']);
	            $v['device_ip'] = $a['ip'];
	            $v['device_hostname'] = is_null($a['name'])?'未设置':$a['name'];

	            $src_hostname = $this->getHostName($v['src_ip']);
	            $v['src_hostname'] = $src_hostname?$src_hostname:'未设置';
	            $dst_hostname = $this->getHostName($v['dst_ip']);
	            $v['dst_hostname'] = $dst_hostname?$dst_hostname:'未设置';
	            $v['agent_ctx'] = $v['agent_ctx']?$v['agent_ctx']:'未设置';
	            $v['sensor_id'] = $v['sensor_id']?$v['sensor_id']:'未设置';
	            $v['src_host'] = $v['src_host']?$v['src_host']:'未设置';
	            $v['src_mac'] = $v['src_mac']?$v['src_mac']:'未设置';
	            $v['src_net'] = $v['src_net']?$v['src_net']:'未设置';
	            $v['dst_host'] = $v['dst_host']?$v['dst_host']:'未设置';
	            $v['dst_mac'] = $v['dst_mac']?$v['dst_mac']:'未设置';
	            $v['dst_net'] = $v['dst_net']?$v['dst_net']:'未设置';

	            $v['sensor_id'] = $a['sensor_id'];


			}

		return is_null($acid_event) ? [] : $acid_event;
	}
	/**
	* 生产redis数据
	*/
	public function production($data,$role=''){
		
		//10分钟过期
		if(empty($role)){
			foreach($data as $ip => $arr){
				$this->set($this->redis_prefix.$ip,json_encode($arr),0,0,120);
			}
		}else{
			$this->set($this->redis_prefix.'1',json_encode($data),0,0,120);
		}

		return true;

	}
	/**  
	*	生产table,生产map
	*  @param $hash String hash表名
	*  @param $sql  String 要同步的sql语句
	*  @param $key  String hash键名
	*  @param $val  String hash键值
	*  @param $expire Int 过期时间，默认10分钟
	*/
	private function production_table(string $hash,string $sql,string $key,string $val = '',int $expire=600){

		if($this->hashLen($hash) == 0){
			$data =  $this->query($sql);
			if(!is_null($data)){
				//当val为空的时候，证明值是一个数组
				if(empty($val)){
					$keys = array();
					$vals = array();
					foreach ($data as $arr){
						$keys[] = $arr[$key];
						$vals[] = json_encode($arr,256);
					} 
					
					$data = array_combine($keys,$vals);

				}else{
					$data = array_combine(array_column($data,$key),array_column($data,$val));

				}
			}else{
				return false;
			}
			//表缓存数据设置为10分钟，即600s
			$this->hashSet($hash,$data);
			$this->setKeyExpire($hash,$expire); 
		}

		return true;

	}
	/**
	*获得国家英文缩写标称
	*@params $ip string IP地址
	*查出国家英文简称，查不出的默认设置为中国
	*/
	private function getisoCode($ip){
		try{
            return $this->country->country($ip)->country->isoCode;
        }catch(\Exception $e){
        	//echo $e->getMessage(),'<br/>';
        	return '';
        }
	}

    /**
	*获得国家中文缩写标称
	*@params $ip string IP地址
	*查出国家英文简称，查不出的默认设置为中国
	*/
	private function getIpCountryName($ip){

		try{
            return $this->country->country($ip)->country->names['zh-CN'];
        }catch(\Exception $e){

        	return '';
        }
	}

	/**  
		协议表 
		protocol [
			number => name
		]
		过期时间一天
	*/
	private function getPro($number){

		$hash = 'table-number-name';
		$status = $this->production_table($hash,"select number,name from alienvault.protocol",'number','name',86400);
		return $status ? $this->hashGet($hash,$number) : false;

	}

	private function getHostName($ip){

		$hash = 'table-ip-hostname';
		$status = $this->production_table($hash,'SELECT `hostname`,INET6_ntoa(ip) ip FROM `host` inner join host_ip on host.id = host_ip.host_id','ip','hostname');
		return $status ? $this->hashGet($hash,$ip) : false;

	}
	private function getDevice($device_id){

		$hash = 'table-id-json';
		$status = $this->production_table($hash,'select a.name,a.ip,b.id,a.host_id as sensor_id from alienvault.udp_sensor a inner join alienvault_siem.device b on inet6_ntoa(b.device_ip) = a.ip','id');
		return $status ? json_decode($this->hashGet($hash,$device_id),true) : false;

	}
	 //plugin_sid 获取事件名称,过期时间一天
    public function getEventName($plugin_idsid)
    {

    	$hash = 'table-plugin_id:sid-name';
		$status = $this->production_table($hash,'select a.name,concat_ws(":", plugin_id,sid) plugin_idsid from alienvault.plugin_sid a','plugin_idsid','name',86400);
		return $status ? $this->hashGet($hash,$plugin_idsid) : false;

    }
}

/** 
生产数据,
创建两个进程，父进程用于用于管理员数据，子进程用于处理子账户安全事件的数据

 */
$pid = pcntl_fork();
if($pid==-1){
   exit('进程创建失败');
}else{
	$Crontab_event = new Crontab_event();

   	if($pid){
		//这里是父进程执行的逻辑
		$data = $Crontab_event->admin();
		$Crontab_event->production($data,'admin');
	}else{
		//这里是子进程执行的逻辑
		$data = $Crontab_event->sensor();
		$Crontab_event->production($data);

   	}
}



?>
