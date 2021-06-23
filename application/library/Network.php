<?php

/***********************************************
 *		网络验证处理类
 ***********************************************/
class Network {

	/**
	* 验证网络
	* 验证成功返回true，验证失败返回，建议网络
	*/
	public function vaildation_network($network){
	  if(strpos($network,'/')){
	    $arr = explode('/',$network);
	    $arr[1] = (int)$arr[1];
	    if(empty($arr[1])){
	    	return '子网掩码为空';
	    }

	    $mask = self::subnetmask($arr[1]);
	    if(filter_var($arr[0],FILTER_VALIDATE_IP)){
	      $num = [
	        ip2long($arr[0]),
	        ip2long($mask),
	      ];


	      $diff = $num[0]&$num[1];

	      return $num[0] == $diff ? true : '网络不合法，建议网络，'.long2ip($diff).'/'.$arr[1];

	    }else{
	      return "IP网络不符合规范";
	    }

	  }else{
	  	return filter_var($network,FILTER_VALIDATE_IP) ? true : 'IP地址不合法'; 
	  }

	}


	/** 计算掩码 */
	private function subnetmask(int $mask){

	  $ret = '';
	  $change = self::calc_change($mask);

	  switch ($mask) {
	    case $mask>0 && $mask<9:
	      $ret = $change.'.0.0.0';
	      break;
	    case $mask>8 && $mask<17:

	      $ret = '255.'.$change.'.0.0';
	      break;
	    case $mask>16 && $mask<25:
	      $ret = '255.255.'.$change.'.0';
	      break;
	    case $mask>24 && $mask<33:
	      # code...
	      $ret = '255.255.255.'.$change;
	      break;
	    default:
	      return '子网掩码不符合规范';
	      break;
	  }

	  return $ret;
	}

	/**
	* 计算差异
	*/
	private function calc_change($mask){
	  $y = 8-$mask%8;
	  $change = $y == 8 ? 255 : 256-pow(2,$y);
	  return $change;
	}

}


?>