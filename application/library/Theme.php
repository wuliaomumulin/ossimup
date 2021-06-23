<?php

/***********************************************
 *		主题类
 ***********************************************/
class Theme {
	//方案
	public $projet = 'status';
	//色系组
	private $colors = [
		//复古
		'fugu' => [
			'#0780cf',
			'#765005',
			'#fa6d1d',
			'#0e2a82',
			'#b6b51f',
			'#da1f18',
			'#701866',
			'#f47a75',
			'#009db2',
			'#024b51',
			'#0780cf',
			'#765005',
		],
		//状态
		'status' => [
			'red',
			'#0173ff',
		]
	];

	/**
	 根据某色系随机返回一个颜色值
	*/
	public function roundColor(){
		return $this->colors[$this->projet][rand(0,sizeof($this->colors[$this->projet])-1)];
	}

	/**
	* 获取连线状态颜色
	* 0:正常:blue;1:断开:red
	*/
	public function status($status){
		return $this->colors[$this->projet][$status];
	}

}