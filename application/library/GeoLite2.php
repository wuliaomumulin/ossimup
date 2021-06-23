<?php
use GeoIp2\Database\Reader;

//使用ip转经纬度
class GeoLite2{

	private $city = null;
	private $country = null;

	public function __construct(){
	
       
	}

	/**
	*  获取IP经纬度
	* return Array 返回经度,纬度
	*/
	public function getlongLat($ip){
		$this->city = new Reader(dirname(__FILE__).'/GeoLite2-City.mmdb'); 
		try{
			$record = $this->city->city($ip);
             return [$record->location->longitude,$record->location->latitude];
        }catch(\Exception $e){
        	//echo $e->getMessage(),'<br/>';
        	return [];
        }
	}

	/**
	*获得国家英文缩写标称
	*@params $ip string IP地址
	*查出国家英文简称，查不出的默认设置为中国
	*/
	public function getisoCode($ip){
		$this->country = new Reader(dirname(__FILE__).'/GeoLite2-Country.mmdb'); 
		
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
	public function getIpCountryName($ip){
		$this->country = new Reader(dirname(__FILE__).'/GeoLite2-Country.mmdb'); 
		try{
            return $this->country->country($ip)->country->names['zh-CN'];
        }catch(\Exception $e){
        	//echo $e->getMessage(),'<br/>';
        	return '';
        }
	}

}