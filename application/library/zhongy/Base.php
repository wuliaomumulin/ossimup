<?php
/**
* 中烟项目-底层类
*/

class zhongy_Base{

	protected $user_device_power;
	protected $user_monitor_power;
    public $index;
    public $redis;

	public function __construct(){
	 	$this->user_device_power = $_SESSION["user_device_power"];
	    $this->user_monitor_power = $_SESSION["user_monitor_power"];
        $this->redis = new \phpredis();
	}

    /**
    * 获取分用户权限
    * @params $type string elasticsearch|mysql
    */
    public function role($type){

        if($this->user_device_power == 'all' && $this->user_monitor_power == 'all'){
            return '';
        }

        $device = array_merge_recursive(array_column(json_decode($this->user_device_power,true),'device_ip'),array_column(json_decode($this->user_monitor_power,true),'device_ip'));
        
        //输出类型封装
        if($type == 'elasticsearch' && !is_null($device)){
            $ret = array(
                "terms" => array(
                    "device" => $device
                )
            );
        }else{
            return '';
        }

        return $ret;
    }

    /**
    * 获取Es的index
    */
    public function eventIndex(){
        $spHttp = new \spHttp();
        //$config = \Yaf\Registry::get("config");
        $url = $_SESSION['ES_HOSTNAME'] . '/_cat/indices?v&h=index&format=json';
        $index = array_column(json_decode($spHttp->vget($url), 256),'index');
        $this->index = $index;
        return $index;
    }
}