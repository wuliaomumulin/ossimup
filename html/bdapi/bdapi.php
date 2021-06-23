<?php
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

require_once dirname(dirname(dirname(__FILE__))) . '/application/library/rsa.php';
require_once dirname(dirname(dirname(__FILE__))) . '/application/library/mysql.php';
/*

查看是否授权外部访问   限制访问ip
*/
$sql_custom = "SELECT * FROM config where conf = 'custorm_host_ip'";
$result = mysqli_query($con, $sql_custom);
$visit_info = [];
while ($row = $result->fetch_assoc()) {
    $visit_info[$row['conf']] = $row['value'];
}

if ($visit_info['custorm_host_ip'] != '0.0.0.0' && $visit_info['custorm_host_ip'] != $_SERVER['REMOTE_ADDR']) {
    die('非法操作!');
}


$redis = new Redis();
$redis->connect('127.0.0.1', 6379); //连接Redis
$redis->select(2);//选择数据库2
/*
限定访问频率  最多每3秒访问一次
 */
$key = 'Andisec.com';
$inc = $redis->incr($key);

if ($inc == 1) {
    //s生存时间      3秒
    $redis->expire($key, 1);

} else {

    jsonResult(null);
}


/*
  访问方式  post/get
 */
$method = $_SERVER['REQUEST_METHOD'];
$rsa = new Rsa();

if ($method == 'GET') {
    $params = json_decode($rsa->publicDecrypt($_GET['params']), 256);                                          //大数据调厂级平台实施数据

} elseif ($method == 'POST') {
    $params = json_decode(gzuncompress($rsa->publicDecrypt($_POST['params'])), 256);
    $params['token'] = $_POST['token'];
    $params['time'] = $_POST['time'];
} else {
    jsonResult(null);
}

/*
   判断访问是否合法
 */

$check = verifyData($params, $redis);

if ($check != '1') {
    jsonResult(null);
}

/*
    POST 进入升级
 */
if ($method == 'POST') {

    $rs = curl_post_https("https://" . $_SERVER['SERVER_NAME'] . "/bdapi/upgrade.php", $_POST);

    echo 'success';
    die;
}

/*
GET 获取数据
 */
if ($method == 'GET') {

    /*
     *  获取实施信息
     */
    if ($params['type'] == 'get_info') {

        //查询实施信息
        $sql_custom = "SELECT * FROM custom_verify";
        $result = mysqli_query($con, $sql_custom);
        $res = [];
        while ($row = $result->fetch_assoc()) {
            $res['custom'][$row['attribute']] = $row['value'];
        }

        /*
         *   获取探针信息
         */
        $sql_sensor = "SELECT * FROM udp_sensor order by subtype DESC";
        $result = mysqli_query($con, $sql_sensor);

        $res['sensor'] = [];
        while ($row = $result->fetch_assoc()) {

            if (in_array($row['subtype'], [1101, 1102, 1103])) {
                $row['status_desc'] = check_status($v['ip'], $con)['status_desc'];
            }
            $res['sensor'][getSensorType($row['subtype'])][] = $row;
        }

        /*
         *    获取版本信息
         */
        exec("cat /version.txt", $version);
        if ($version) {
            $ver = get_between($version[0], 'V', '-');
        }

        $res['version'] = $ver ? $ver : 'unkonw';

    }

    $con->close();
    // 实际情况  压缩后 不base64 会解压失败  base64后文件大小会变成原来的3/4  小数据量无意义，大数据量也无意义  压缩等级过高容易解压失败
    jsonResult($res);
}
//jsonResult(null);


// 检测采集器是否在线状态

function check_status($ip, $con)
{

    if (!$con) {
        die("Error:" . mysqli_error());
    }
    $log_status = "SELECT * FROM alienvault_siem.acid_event as a join alienvault_siem.device as d where a.plugin_id = 100000 and a.device_id = d.id and a.timestamp >= DATE_SUB(NOW(),INTERVAL 5 MINUTE ) and INET6_NTOA(d.device_ip) = '{$ip}'";
    $log_type = "SELECT * FROM alienvault_siem.acid_event as a join alienvault_siem.device as d where a.plugin_id = 100005 and a.device_id = d.id and a.timestamp >= DATE_SUB(NOW(),INTERVAL 5 MINUTE ) and INET6_NTOA(d.device_ip) = '{$ip}'";
    $log_status = mysqli_query($con, $log_status);
    $log_type = mysqli_query($con, $log_type);
    $status = 1;
    $status_desc = '';

    if ($log_status->num_rows == 0) {
        $status = 0;
        $status_desc = '日志状态不通|';
    }

    if ($log_type->num_rows == 0) {
        $status = 0;
        $status_desc .= '交换机syslog不通';
    }

    return ['status' => $status, 'status_desc' => $status_desc];
}

//截取版本号
function get_between($input, $start, $end)
{
    $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
    return $substr;
}

function getSensorType($item)
{

    $arr['1101'] = '92';
    $arr['1102'] = '91';
    $arr['1103'] = '90';
    $arr['1104'] = '89';
    $arr['1105'] = '88';

    if (!empty($item)) return $arr[$item];

    return $arr;

}

function curl_post_https($url, $data,$head = '',$cookies = '')
{ // 模拟提交数据函数
    $header = array(
        'Cookie: '.$cookies,
        $head,
    );

    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);// 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式

}

function verifyData($data, $redis)
{

    if (!empty($data)) {

        //超频限制


        //$redis->del($data['token']);

        $num = $redis->incr($data['token']);//redis 自增   $i++

        //不等于1 请求失败
        if ($num == 1) {
            //单个token 60*60*24内不可以再次访问
            $redis->setTimeout($data['token'], 86400);
            //验证请求是否超时
            $time = time() - intval($data['time']);
            //任意一个请求五分钟之内有效
            //   if ($time <= 0 || $time >= 42300) return null;


            //生成新的token 并验证Token有效性
            // if ($data['token'] != getToken($data)) return null;

            return 1;

        } else {
            return null;
        }
    }
    return null;
}

/**
 * @abstract 获取Token
 * @return
 */
function getToken($data)
{

    unset($data['token']);



    //字典排序
    ksort($data);

    //生成指定的字符串
    $str = '';
    foreach ($data as $k => $v) {
        $str .= $k . '=' . $v . '&';
    }
    $str .= 'andisec.com';

    $str = md5($str);

    return $str;
}


function jsonResult($res)
{
    echo base64_encode(gzcompress(json_encode($res, 256), 4));
    die;
}




