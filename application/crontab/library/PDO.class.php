<?php
/**
 * PDO php class
 * author lin <wuliaomumulin@163.com>
 * version 1.0
 */
	class DB{
		protected static $_instance = null;
		protected $dbName = '';
		protected $dsn;
		protected $dbh;
		//是否开启debug模式
		public $debug = false;
		/**
		 * 构造函数
		 */
		public function __construct($dbHost,$dbUser,$dbPasswd,$dbName,$dbCharset){
			try{
				$this->dsn = 'mysql:host='.$dbHost.';dbname='.$dbName;
				$this->dbh = new PDO($this->dsn,$dbUser,$dbPasswd);
				$this->dbh->exec('set character_set_connection='.$dbCharset.', character_set_results='.$dbCharset.', character_set_client=binary');
			}catch(PDOException $e){
				$this->outputError($e->getMessage());
			}
		}
		/**
		 * 防止克隆
		 */
		private function __clone(){}
		/**
		 * 实例化一个数据库
		 * @param  [string] $dbName    
		 * @param  [string] $dbUser    
		 * @param  [string] $dbPasswd  
		 * @param  [string] $dbName    
		 * @param  [string] $dbCharset 
		 * @return [object] 数据库实例  
		 */        
		public function getInstance($dbHost,$dbUser,$dbPasswd,$dbName,$dbCharset){
			if(self::$_instance==null){
				self::$_instance = new self($dbHost,$dbUser,$dbPasswd,$dbName,$dbCharset);
			}
			return self::$_instance;
		}
		/**
		 * [query description]
		 * @param  [string]  $sqlstr    [查询字符串]
		 * @param  string  $querymode [查询模式，All:全部|Row：单条]
		 * @return [array]    $result         [数据]
		 */
		public function query($sqlstr,$querymode='All'){
				if($this->debug==true) return $sqlstr;
				$recordset = $this->dbh->query($sqlstr);
				$this->getPDOError();
				if($recordset){
					$recordset->setFetchMode(PDO::FETCH_ASSOC);
					if($querymode=='All'){
						$result = $recordset->fetchAll();
					}elseif($querymode=='Row'){
						$result = $recordset->fetch();
					}
				}else{
					$result = '';
				}
				return $result;
		}
		/**
		 * [update 更新]
		 * @param  [type] $table          [表名]
		 * @param  [type] $arrayDataValue [数据]
		 * @param  string $where          [条件]
		 * @return [type]                 [description]
		 */
		public function update($table,$arrayDataValue,$where=''){
			$this->checkFields($table,$arrayDataValue);
			if($where){
				$strSql = '';
				foreach($arrayDataValue as $key => $val){
					$strSql.=','.$key.'="'.$val.'"';
				}
				$strSql = substr($strSql,1);
				$strSql = 'update `'.$table.'` set '.$strSql.' where '.$where;
			}else{
				$strSql = 'replace into `'.$table.'`('.implode(',',array_keys($arrayDataValue)).') values("'.implode('","',$arrayDataValue).'")';
			}
			$result = $this->execSql($strSql);
			return $result;
		}
		/**
		 * [insert 插入]
		 * @param  [type]  $table          [表名]
		 * @param  [type]  $arrayDataValue [数据]
		 * @return [type]                  [description]
		 */
		public function insert($table,$arrayDataValue){
			$this->checkFields($table,$arrayDataValue);			
			$strSql = 'insert into `'.$table.'`('.implode(',',array_keys($arrayDataValue)).') values("'.implode('","',$arrayDataValue).'")';
			$result = $this->execSql($strSql);
			return $result;
		}
		/**
		 * [insert 插入一簇]
		 * @param  [type]  $table          [表名]
		 * @param  [type]  $arrayDataValue [数据]
		 * @return [type]                  [description]
		 */
		public function insertCluster($table,$arrayDataValue){
			$this->checkFields($table,$arrayDataValue[0]);
			$val = '';
			foreach ($arrayDataValue as $arr) $val .= '("'.implode('","',$arr).'"),';
			$strSql = 'insert into `'.$table.'`('.implode(',',array_keys($arrayDataValue[0])).') values'.rtrim($val,',');
			$result = $this->execSql($strSql);
			return $result;
		}
		/**
		 * [replace 覆盖,需有主键ID]
		 * @param  [type]  $table     
		 * @param  [type]  $arrayDataValue      
		 * @return [type]                  
		 */
		public function replace($table,$arrayDataValue){
			$this->checkFields($table,$arrayDataValue);
			$strSql = 'replace into `'.$table.'`('.implode(',',array_keys($arrayDataValue)).') values("'.implode('","',$arrayDataValue).'")';
			$result = $this->execSql($strSql);
			return $result;
		}
		/**
		 * [insert 覆盖一簇,需有主键ID]
		 * @param  [type]  $table          [表名]
		 * @param  [type]  $arrayDataValue [数据]
		 * @return [type]                  [description]
		 */
		public function replaceCluster($table,$arrayDataValue){
			$this->checkFields($table,$arrayDataValue[0]);
			$val = '';
			foreach ($arrayDataValue as $arr) $val .= '("'.implode('","',$arr).'"),';
			$strSql = 'replace into `'.$table.'`('.implode(',',array_keys($arrayDataValue[0])).') values'.rtrim($val,',');
			$result = $this->execSql($strSql);
			return $result;
		}
		/**
		 * [delete 删除]
		 * @param  [string]  $table 
		 * @param  string  $where 
		 * @return [type]         
		 */
		public function delete($table,$where=''){
			if($where == ''){
				$this->outputError('where is Null');
			}else{
				$strSql = 'delete from `'.$table.'` where '.$where;
				$result = $this->execSql($strSql);
				return $result;
			}
		} 
		/**
		 * [execSql 执行sql]
		 * @param  [type]  $strSql 
		 * @return [type]   
		 */
		public function execSql($strSql){
			if($this->debug==true) return $strSql;
			$result = $this->dbh->exec($strSql);
			$this->getPDOError();
			return $result;
		}
		/**
		 * [getMaxValue 获取最大值]
		 * @param  [type]  $table     
		 * @param  [type]  $fields_name 
		 * @param  string  $where          
		 * @return [type]               
		 */
		public function getMaxValue($table,$fields_name,$where=''){
			$strSql = 'select max('.$fields_name.') as MAX_VALUE from `'.$table.'`';
			if($where!='') $strSql.=' where '.$where;
			if($this->debug==true) return $strSql;
			$arrTemp = $this->query($strSql,'Row');
			$maxValue = $arrTemp['MAX_VALUE'];
			if($maxValue==''||$maxValue==Null){
				$maxValue = 0;
			}
			return $maxValue;
		}
		/*获取字段总数*/
		public function getCount($table,$fields_name,$where=''){
			$strSql = 'select count('.$fields_name.') as NUM from `'.$table.'`';
			if($where!='') $strSql.=' where '.$where;
			if($this->debug) return $strSql;
			$arrTemp = $this->query($strSql,'Row');
			return $arrTemp['NUM'];
		}
		/*获得表引擎*/
		public function getTableEngine($dbName,$tableName){
			$strSql = 'show table status from '.$dbName.' where name="'.$tableName.'"';
			$arrayTableInfo=$this->query($strSql);
			$this->getPDOError();
			return $arrayTableInfo[0]['Engine'];
		}
		/*开始事务*/
		private function beginTransaction(){
			$this->dbh->beginTransaction();
		}
		/*提交事务*/
		private function commit(){
			$this->dbh->commit();
		}
		/*火棍事务*/
		private function rollback(){
			$this->dbh->rollback();
		}
		/*执行事务*/
		public function execTransaction($arraySql){
			$retval = 1;
			$this->beginTransaction();
			foreach($arraySql as $strSql){
				if($this->execSql($strSql)==0) $retval=0;
			}
			if($retval==0){
				$this->rollback();
				return false;
			}else{
				$this->commit();
				return true;
			}
		}		
		/**
		 * [checkFields 检查指定字段是否在指定数据表中存在]
		 * @param  [string] $table          [表名]
		 * @param  [array]  $arrayDataValue [数据：字段名：数据]
		 */
		private function checkFields($table,$arrayFields){
			$fields = $this->getFields($table);
			if($this->debug==true) return $fields;
			foreach ($arrayFields as $key => $value) {
				if(!in_array($key,$fields)){
					$this->outputError('unkown columns'.$key.' in fields list');
				}
			}
		}
		/**
		 * [getFields 获得表字段]
		 * @param  [string] $table [表名称]
		 * @return [array]  $fields [表字段数组]
		 */
		private function getFields($table){
			$fields = array();
			$strSql = 'show columns from `'.$table.'`';
			if($this->debug==true) return $strSql;
			$recordset = $this->dbh->query($strSql);
			$this->getPDOError();
			$recordset->setFetchMode(PDO::FETCH_ASSOC);
			$result = $recordset->fetchAll();

			foreach ($result as $a) {
				$fields[]=$a['Field'];
			}

			return $fields;
		}
		/**
		 * 捕捉PDO错误异常信息
		 * @return [type] [description]
		 */
		private function getPDOError(){
			if($this->dbh->errorCode()!='00000'){
				$arrayError = $this->dbh->errorInfo(); 
				$this->outputError($arrayError[2]);
			}
		}
		/**
		 * [outputError 输出错误信息]
		 * @param  [type] $strErrMsg [错误信息]
		 * @return [type]            [description]
		 */
    private function outputError($strErrMsg)
    {
        throw new Exception('MySQL Error: '.$strErrMsg);
    }
	/**
	 * [destruct 关闭数据库连接]
	 * 
	 */
	public function destruct(){
		$this->dbh = null;
	}
}
?>



