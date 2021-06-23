<?php
/**
 * Created by PhpStorm.
 * User: wangcheng
 * Date: 2020/1/14
 * Time: 15:20
 */

require_once 'av_init.php';
if (Session::get_session_user() == '') {
    header("Location: /ossim");
    exit();
}

$db    = new ossim_db();
$conn  = $db->connect();
$db->fetchMode=2;//设为关联数组
$command_server="localhost";
/**/


$result = $_POST;
if($result['type'] != 'check'){
   die;
}
unset($result['type']);
$b =[];
foreach ($result as $k=>$v){
    $b[substr($k,0,strpos($k,'_'))][substr($k,strpos($k,'_')+1)] = $v;
}

$data = [];
if($b){
    foreach ($b as $k => $v){
        $data[$k.'_ip']['status'] = 1;
        $data[$k.'_ip']['detail'] = '';
        //未实施的不检测
        if ($v['status'] == 0) continue;
        //备平台只检测 ping 通不通
        if ($k == 'backup' ){
            $ping_result = json_decode(ping_time($v['ip']),true);
            if($ping_result['code'] != 200){
                $data[$k.'_ip']['status'] = 0;
                $data[$k.'_ip']['detail']  = '备平台不通';
            }
            continue;

        }

        $arr = explode(",",$v['ip']);
        foreach ($arr as $key => $val){
            $log_status = resolvData($conn->Execute("SELECT * FROM alienvault_siem.acid_event as a join alienvault_siem.device as d where a.plugin_id = 100000 and a.device_id = d.id and a.timestamp >= DATE_SUB(NOW(),INTERVAL 5 MINUTE ) and INET6_NTOA(d.device_ip) = '{$val}'"));
            $log_type = resolvData($conn->Execute("SELECT * FROM alienvault_siem.acid_event as a join alienvault_siem.device as d where a.plugin_id = 100005 and a.device_id = d.id and a.timestamp >= DATE_SUB(NOW(),INTERVAL 5 MINUTE ) and INET6_NTOA(d.device_ip) = '{$val}'"));

            if(empty($log_status)){
                $data[$k.'_ip']['status'] = 0;
                $data[$k.'_ip']['detail'] .= $val.':日志状态不通|';
            }

            if(empty($log_type)){
                $data[$k.'_ip']['status'] = 0;
                $data[$k.'_ip']['detail'] .= $val.':交换机syslog不通|';
            }
        }

    }

}

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://'.$command_server.':8080/interface/edit?type=get_syslog');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($curl);
curl_close($curl);

$sys_log = json_decode($res,true);
$ping_res = json_decode(ping_time($sys_log['host']),true);
if($ping_res['code'] != 200){
    $data['bloc_status'] = '与集团ping不通';
}

echo json_encode(['code' => 200,'data' =>$data]);

//接受一个对象，返回一个数组
function resolvData($rs){
    if(is_object($rs)){
        while (!$rs->EOF)
        {
            $res[]=$rs->fields;
            $rs->MoveNext();
        }
        return $res;
    }
}

function ping_time($ip) {
    $ping_cmd = "ping -c 1 -w 5 " . $ip;
    exec($ping_cmd, $info);
    if($info == null)
    {
        return json_encode(['code'=>404,'msg'=>"Ping请求找不到主机".$ip.";请检查该名称,然后重试"]);die;
    }
    //判断是否丢包
    $str1 = $info['4'];
    $str2 = "1 packets transmitted, 1 received, 0% packet loss";
    if( strpos( $str1 , $str2 ) === false)
    {
        return json_encode(['code'=>403,'msg'=>"当前网络堵塞,请求无法成功,请稍后重试"]);die;
    }


    $ping_time_line = end($info);
    $ping_time = explode("=", $ping_time_line)[1];
    $ping_time_min = explode("/", $ping_time)[0] / 1000.0;
    $ping_time_avg = explode("/", $ping_time)[1] / 1000.0;
    $ping_time_max = explode("/", $ping_time)[2] / 1000.0;

    $result = array();
    $result['domain_ip'] = $info['0'];
    $result['ping_min'] = $ping_time_min;
    $result['ping_avg'] = $ping_time_avg;
    $result['ping_max'] = $ping_time_max;

    return json_encode(['code'=>200,'msg'=>"请求成功",'data'=>$result]);
}
?>