<?php
/***********************************************
 *		Elasticsearch请求类
 ***********************************************/


class Elasticsearch{

	//设置索引
	public $url = '';
	//类型
	public $type = '_doc';
	//请求头
	protected $head = "Content-Type: application/json"; 
	//

	public function __construct(){
		$this->url =  $_SESSION['ES_HOSTNAME'];
	}

	//单个字段设置值,局部更新
	public function setFiled($key='',$value=''){
        if(empty($key) || empty($value)) return '无效的键名或值';

        $spHttp = new \spHttp();

        $params = [
        	$this->type => [
        		$key => $value
        	] 
        ];

        $ret = json_decode($spHttp->vpost($this->url, json_encode($params), $this->head), 256);

        return $ret['result'];
	}

	/**
	* 设置index
	* 局部更新 example /index/_doc/1/_update
	*/
	public function setIndex($index){
		$this->url .= $index;
		return 0;
	}


}
?>