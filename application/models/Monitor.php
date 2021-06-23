<?php
namespace App\Models;
/* 
 * 监控项--审计产品
 */
class Monitor extends Model
{

    //探针路径
    static private $agent_src = 'python /work/sensor_manager/agent/';
    //错误信息
    static public $errors = ''; 

    public function allAgent($page,$pagesize){
        
        $data = json_decode($_SESSION["user_monitor_power"],1);
        $subtype = [];
        if(!empty($data)){
            $subtype = array_values(array_column($data, "device_type"));
        }elseif($_SESSION["user_monitor_power"] == 'all'){
            $subtype = ['1201','1202','1203'];
        }

        $where = [
            'subtype' => ['in',$subtype]
        ];
 
        $ret = $this->table('udp_sensor')->field('ip,name,port,ver,host_id')->where($where)->order('ctime desc')->page($page, $pagesize)->select();
        
        /*
         获得资产附加属性
        */
        if (!\Tools::isEmpty($ret)) {
            array_walk($ret, function (&$arr) {

                $status =  \IpLocation::nc_port($arr['ip'],$arr['port']);
                $arr['status'] = stripos($status,'ok') > -1 ? true : false;
            });
        }else{
            $ret = [];
        }


        return $ret;
    }

	/***
    * 获取网络白名单
    */
    public function WhiteList($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];
        $whe['para'] = $where['para'];

        //cli模式获取参数
        $command = static::$agent_src."collect_config.py -t {$whe['ip']} -p {$whe['port']} --para={$whe['para']}";
        exec($command,$res,$status);

        $ret = [];

        if($status==0){

            $file = '/work/sensor_manager/agent/return/monitor/0x10';

            if(file_exists($file) and is_readable($file) and is_writeable($file)){
                $hd = file($file);
                if(!empty($hd)){
                    for($i=0;$i<sizeof($hd);$i++){
                        $ret[] = static::formatListItem($hd[$i],($i+1));
                    }                   
                }
            }
            
            return $ret;
        }else{

            static::$errors = implode('',$res);
            return False;
        }    
    }

    /**
    * 数据源整理
    * $string 字符串
    * $num 行号
    */
    private static function formatListItem($string,$num){
        $string = rtrim($string,"\n");
        $result = explode(',',$string);
        array_unshift($result,$num);
        $keys = ['id','item'];

        return array_combine($keys,$result);
    }

    /***
    * 保存白名单
    */
    public function saveWhite($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];
        $whe['para'] = $where['para'];

        //cli模式获取参数
        $command = static::$agent_src."collect_config.py -t {$whe['ip']} -p {$whe['port']} --para={$whe['para']}";
        exec($command,$res,$status);


        if($status==0){
            $file = '/work/sensor_manager/agent/return/monitor/0x10';
            if(file_exists($file) and is_readable($file) and is_writeable($file)){

                //白名单去重
                if($this->whiteList_unique($file,$where['item'])){

                    if($where['id'] == 0){
                        $item = "{$where['item']}";
                        $command2 = "test -s {$file} && sed -i '\$a {$item}' {$file} || echo '{$item}' >> {$file}";
                        `{$command2}`;
                    }else{
                        $item = "{$where['item']}";
                        $command2 = "sed -i '{$where['id']}c {$item}' {$file}";
                        `{$command2}`;
                    }
                    $command = static::$agent_src."config_audit_system.py -t {$whe['ip']} -p {$whe['port']} --subtype={$whe['para']}";
                    $ret = `{$command}`;
                    return true;

                }else{
                    static::$errors = '该网络已经存在于白名单中';
                    return False;
                }

            }
            return False;
        }else{
            static::$errors = implode('',$res);
            return False;
        }    
    }    
    /**
    * 规则列表--白名单-删除
    */
    public function destroyWhite($where){
        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];
        $whe['para'] = $where['para'];

        //cli模式获取参数
        $command = static::$agent_src."collect_config.py -t {$whe['ip']} -p {$whe['port']} --para={$whe['para']}";
        exec($command,$res,$status);
        if($status==0){
            $file = '/work/sensor_manager/agent/return/monitor/0x10';
            if(file_exists($file) and is_readable($file) and is_writeable($file)){
                $del = "sed -i {$where['id']}d {$file}";
		          exec($del,$res,$status);

                $command = static::$agent_src."config_audit_system.py -t {$whe['ip']} -p {$whe['port']} --subtype={$whe['para']}";

                $ret = `{$command}`;
                return true;
            }
            return False;
        }else{
            static::$errors = implode('',$res);
            return False;
        } 
    }


    /***
    * 获取网络协议
    */
    public function ProtocolList($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];
        $whe['para'] = $where['para'];

        //cli模式获取参数
        $command = static::$agent_src."collect_config.py -t {$whe['ip']} -p {$whe['port']} --para={$whe['para']}";
        exec($command,$res,$status);

        $ret = [];

        if($status==0){

            $file = '/work/sensor_manager/agent/return/monitor/0x11';

            if(file_exists($file) and is_readable($file) and is_writeable($file)){
                $hd = file($file);
                if(!empty($hd)){
                    for($i=0;$i<sizeof($hd);$i++){
                        $ret[] = static::ProtocolformatListItem($hd[$i],($i+1));
                    }                   
                }
            }
            
            return $ret;
        }else{

            static::$errors = implode('',$res);
            return False;
        }    
    }

    /**
    * 数据源整理
    * $string 字符串
    * $num 行号
    */
    private static function ProtocolformatListItem($string,$num){
        $string = rtrim($string,"\n");
        $result = explode('::',$string);
        array_unshift($result,$num);
        $keys = ['id','protocol','status'];

        return array_combine($keys,$result);
    }

    /***
    * 保存协议
    */
    public function saveProtocol($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];
        $whe['para'] = $where['para'];

        //cli模式获取参数
        $command = static::$agent_src."collect_config.py -t {$whe['ip']} -p {$whe['port']} --para={$whe['para']}";
        exec($command,$res,$status);


        if($status==0){
            $file = '/work/sensor_manager/agent/return/monitor/0x11';
            if(file_exists($file) and is_readable($file) and is_writeable($file)){
                if($where['id'] == 0){
                    $item = "{$where['protocol']}::{$where['status']}";
                    $command2 = "test -s {$file} && sed -i '\$a {$item}' {$file} || echo '{$item}' >> {$file}";
                    `{$command2}`;
                }else{
                    $item = "{$where['protocol']}::{$where['status']}";
                    $command2 = "sed -i '{$where['id']}c {$item}' {$file}";
                    `{$command2}`;
                }
                $command = static::$agent_src."config_audit_system.py -t {$whe['ip']} -p {$whe['port']} --subtype={$whe['para']}";
                $ret = `{$command}`;
                return true;
            }
            return False;
        }else{
            static::$errors = implode('',$res);
            return False;
        }    
    }


    /***
    * 资产扫描
    */
    public function scan($where){

        $whe['ip'] = $where['ip'];
        $whe['port'] = $where['port'];

        //cli模式获取参数
        $command = static::$agent_src."scan_asset.py -t {$whe['ip']} -p {$whe['port']}";

        exec($command,$res,$status);

        $ret = [];

        if($status==0){

           return true;
            
        }else{

            static::$errors = implode('',$res);
            return False;
        }    
    }

    /**
    * 监测审计--白名单去重判断
    *  @$file string 文件名
    *  @$contract string 对比集，网络或者IP
    */
    private function whiteList_unique($file,$contract){      
        $reference = file($file);
        $contract .= "\n";//一个换行
        return in_array($contract,$reference) ? false : true;
    }
}
