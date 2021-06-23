<?php
/***
* decsr:统计网卡流量脚本
* @liyb
*
* 一、前提:开启服务前，请保证redis扩展和shell_exec函数处于开启状态

* 二、服务使用
* 		nohup php network.php > /dev/null 2&1 &开启服务
* 		php network.php& 以后台运行的方式开启服务,该条命令会返回pid，关闭服务的时候使用
*       ps -ef|grep network.php 查找该进程的pid
* 		kill -9 pid 来关闭服务

*/
try{
	$NetworkBytes = new NetworkBytes();
	$NetworkBytes->run();

}catch(\Exception $e){
	echo $e->getMessage();
}






//3、存入redis
/*
lrange network-bytes 0 10 #指定范围获取列表值
rpush network-bytes 100 #向列表压入数据
lpushx network-bytes 105 #向列表头部压入数据
rpop network-bytes #弹出最后一个元素
llen network-bytes #获取列表长度
*/


/***
* decsr:统计网卡流量脚本
 @liyb
*/
class NetworkBytes{
	//redis句柄
	public $redis = Null;
	//设置抓取网卡名称	
	static public $networkName = 'eth5';	
	//操作redis-table的名称
	public $table = 'network-bytes';
	//设置队列长度,30个
	public $listLen = 30;
	//抓取单位,KB or MB
	static private $unit = 'MB';

	//初始化上一秒值为0
	static private $prev = 0;
	//顺序,正序asc|倒叙desc
	static private $order = 'asc'; 

	//@params $environ product|develop
	public function __construct($environ='product'){
		self::connect(self::config($environ));
		//网卡
		$networkName = self::getNetwork();
		if(!empty($networkName)){
			self::$networkName = $networkName;
		}
	}
	/*
	   配置信息
	   @params $environ product|develop
	   @return Array;		
	*/
	private static function config($environ){
		//初始化返回值
		$config = [];
		//定义配置文件位置
		$filename =  dirname(dirname(dirname(__FILE__))).'/conf/application.ini';
		$str = `awk '/redis.config/' {$filename}`;
		
		//获取最后一块的配置
		$ip = '((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}';
		$preg = "/redis\.config\.server=\'({$ip})\'/i";
		preg_match_all($preg,$str,$res);
		$config['hostname'] = ($environ=='product') ? $res[1][0] : $res[1][1];

		$preg = "/redis\.config\.port=\'(\d+)\'/i";
		preg_match_all($preg,$str,$res);
		$config['port'] = ($environ=='product') ? $res[1][0] : $res[1][1];
		return $config;
	}
	/*
	 	初始化连接句柄
	*/
	private function connect($config){
		$redis = new Redis();
		$redis->connect($config['hostname'],$config['port']);
		$this->redis = $redis;
	}
	/*
	  业务操作
	*/
	public function run(){
		//常驻进程
		while(True){			
			//沉睡1s，根据服务器性能进行配置，如果性能好的话，去掉即可
			sleep(1);

			//保存网卡流量
			self::procducer();
		}
	}
	/**
	*	统计网络流量脚本
	*/
	private function procducer(){
		$prev = self::$networkName;
		$str = `ifconfig {$prev}`;//指定网卡名称

		/*
			判断设备是否正常
			为避免CPU做无意义的空转，如果代码有误就关掉该程序
		*/
		if(empty($str)) exit();	

		if(static::$unit == 'MB'){
			$preg = '/TX packets \d+ +bytes (\d+) /i';//MB
			preg_match($preg,$str,$res);
			@$current = $res[1];
			$offset = sprintf("%.1f",($current-static::$prev)/1024/1024);
			//echo 'aaa'.$current.'---'.static::$prev.'---'.$offset.'aaa'.PHP_EOL;
		}

		if(static::$unit == 'KB'){
			$preg = '/TX packets \d+ +bytes (\d+)/i';//比特位
			preg_match($preg,$str,$res);
			$current = $res[1];
			$offset = round(($current-static::$prev)/1024);
		}

		switch (static::$order){
			case 'asc':
				//这里需要检测如果使一次，我们创建一个空链表
				if($this->redis->llen($this->table) == 0){
					$this->redis->rpush($this->table,0);
					break;
				}

				if($this->redis->llen($this->table) == $this->listLen){
					$this->redis->lpop($this->table);
				}

				$this->redis->rpush($this->table,$offset);
				break;
			case 'desc':
				if($this->redis->llen($this->table) == $this->listLen){
					$this->redis->rpop($this->table);
				}
				$signal = $this->redis->lpushx($this->table,$offset);
				//如果是第一次操作，我们创建一个空链表
				if($signal === 0){
					$this->redis->lpush($this->table,$offset);
				}
				break;
			default:
				# code...
				break;
		}

		//将当前值设为过期，就是上一秒值
		static::$prev = $current;

	}

	/*
     * 访问网卡
     * return 网卡名称或者空
     * */
    private function getNetwork()
    {
        $curl = curl_init();
        //http://locahost:8080/interface/edit?type=interface
        curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1/api.php/configsystem/service');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($curl),255);

        curl_close($curl);

        //
        if(isset($data['data']['interface_port']) and !empty($data['data']['interface_port']) and !is_null($data['data']['interface_port'])){
        	return $data['data']['interface_port'];
        }else{
        	return '';
        }
    }
}
?>
