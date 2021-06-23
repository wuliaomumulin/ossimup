<?php
/*
* 跨进程存储binrary和file的缓存方案
* 实例化该类，如果本类的受保护的方法,调用_set方法，其实是调用的基类的cache方法;
*/
class cache_Apcu{
    public function __construct() {
	
	}

	public function _set($key, $value, $ttl = 0) {
		return apcu_store($key, $value, $ttl);
	}

	public function _get($key){
		return apcu_fetch($key);
	}


	public function _exists($key){
		return apcu_exists($key);
	}

	public function _delete($key){
		return apcu_delete($key);
	}

	public function _writeKeys() {
	}

	public function flush() {
		return apcu_clear_cache();
	}
}

