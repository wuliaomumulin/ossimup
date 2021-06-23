<?php

namespace App\Models;

class ThreatIntelligence extends Model{
    
    protected $dbName = 'alienvault_siem';
    protected $tableName = 'intelligence';
    protected $tablePrefix = 'threat_';
    protected $pk = 'id';

    //
    protected $conf = './list_conf/threatIntelligence.json';
    /**
    * 获取列表
    */

    public function getList($where = [],$page,$pagesize,$order='a.timestamp desc')
    {

        /** 加一些测试用例 */
        /*$tables = $this->getTables();
        var_dump($tables);*/

         //配置config
        $json = file_get_contents($this->conf);
        $config = json_decode($json, 256);

        $result['config'] =  $config;
        //field
        $field = 'hex(id) id,severity,family,detail,status,timestamp';
    	
        $result['list'] = $this->comment('查询结果集')->field($field)->alias('a')->where($where)->order($order)->page($page,$pagesize)->select(); 

        $result['total_num'] = $this->alias('a')->where($where)->count();
        return $result;
    }

    // 查询成功后的回调方法
    protected function _after_select(&$resultSet, $options) {
        //将数据化解
        if(!\Tools::isEmpty($resultSet)){

            if(\Tools::is_single_array($resultSet)){
               // var_dump($resultSet);
            }else{
                foreach ($resultSet as &$a) {

                   $str = $a['detail'];
                   $str = preg_replace('/[\x00-\x1F\x80-\x9F]/u','',$str);
                   $detail = json_decode($str,true);



                    //exit(json_last_error());


                    if(!empty($detail)){
                        $a['ioc_raw'] = $detail['ioc_raw'];
                        $a['created_at'] = is_numeric($detail['created_at']) ? date('Y-m-d H:i:s',$detail['created_at']) : $detail['created_at'];
                        $a['find_at'] = is_numeric($detail['find_at']) ? date('Y-m-d H:i:s',$detail['find_at']) : $detail['find_at'];
                        $a['update_at'] = is_numeric($detail['update_at']) ? date('Y-m-d H:i:s',$detail['update_at']) : $detail['update_at'];
                        $a['port'] = $detail['port'];
                        $a['related_sample'] = json_encode($detail['related_sample']);
                        $a['related_ip'] = json_encode($detail['related_ip']);
                        $a['related_gangs'] = $detail['related_gangs'];
                        $a['related_gangs_desc'] = $detail['related_gangs_desc'];
                        $a['related_events_and_desc'] = json_encode($detail['related_events_and_desc']);
                        $a['solution'] = $detail['solution'];
                    }
                     unset($a['detail']);
               }
            }

        }
    }
   
}