<?php
/**
* 这是一个测试unpack/pack的东西
*/

class db{
	/**
	* @param $_database String 数据库文件地址
	*/
	private $_database = './lin.db';
	/**
	* @param $database String 打开一个数据库文件
	*/

	public function __construct($database='./lin.db'){
		$this->_database = $database;
		$this->_database = fopen($this->_database,'ab+');
	}

	public function execute($data){
		$line = pack('a12',$data['name']).pack('S',$data['age']).pack('a30',$data['email']);
		fwrite($this->_database,$line);
	}

	public function query($count = 0){
		rewind($this->_database);
		fseek($this->_database, 40 * $count);
		$ret = [];
		$ret['name'] = unpack('a12',fread($this->_database,12));
		$ret['name'] = trim($ret['name'][1]);
		$ret['age'] = unpack('S',fread($this->_database,2));
		$ret['age'] = trim($ret['age'][1]);
		$ret['email'] = unpack('a30',fread($this->_database,30));
		$ret['email'] = trim($ret['email'][1]);
		return $ret;
	}
}


$data = array(
	'name' => '法外狂徒',
	'age' => 32,
	'email' => 'zhangsan@163.com'
);
$db = new db();
$db->execute($data);
var_dump($db->query(2));
// $p=pack('a12',$data['name']);
// var_dump($p);
// var_dump(unpack('a12',$p));

?>