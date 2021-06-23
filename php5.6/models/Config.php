<?php
namespace App\Models;

class Config extends Model{
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';

    /**
	* 系统状态链接
     */
    public function system_status_chart(){
    	$result = $this->where(['conf'=>'system_status_chart'])->getField('`value`');

    	return (!\Tools::isEmpty($result)) ? ($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$result) : false;
    }
    /**
    * 获取业务网口
    */
    public function getEth(){
        $result = $this->where(['conf'=>'frameworkd_eth'])->getField('`value`');
        return (!\Tools::isEmpty($result)) ? $result : false;
    }
}