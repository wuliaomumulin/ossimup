<?php
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

require_once dirname(dirname(dirname(__FILE__))) . '/application/library/bytes.php';
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
$bytes = new Bytes();

if ($method == 'GET') {
    $params = json_decode(gzuncompress($bytes->superLongPrivateKeyEncrypt($_GET['params'], false, true)), 256);  //智慧能源

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
GET 获取数据
 */
if ($method == 'GET') {

    if ($params['type'] == 'get_wisdom_energy') {

        $data = get_zhny_data($params['table'], base64_decode($params['params']));

        $res = $bytes->superLongPrivateKeyDecrypt(gzcompress($data),false,true);

        echo $res;die;
        // $res = format($data);
    }

    $con->close();

}
//jsonResult(null);

function curl_post_https($url, $data, $head = '', $cookies = '')
{ // 模拟提交数据函数
    $header = array(
        'Cookie: ' . $cookies,
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


function get_zhny_data($tables = 'zhny_enterprise_report', $params = [])
{

    if (empty($params)) return '无效的参数';
//curl -XGET "http://localhost:9200/zhny_enterprise_report-*/_search" -H 'Content-Type: application/json' -d'{  "query": {    "match_all": {}  }}'
    $params = is_array($params) ? json_encode($params) : $params;

    $url = "localhost:9200/" . $tables . "*/_search";
    $head = "Content-Type: application/json";
    $ret = curl_post_https($url, $params, $head);
    return $ret;
}
//
//function format($data)
//{
//    $res = [];
//    foreach ($data['aggregations']['tag']['buckets'] as $k => &$v) {
//        foreach ($v['top']['hits']['hits'] as $kk => &$vv){
//            $res[] = $vv['_source'];
//        }
//    }
//    return $res;
//}

