<?php
/***
 * 函数库
 * @author jsyzchenchen@gmail.com
 * date 2015-11-30
 */

/**
 * 获取输入参数 支持过滤和默认值 From ThinkPHP 系统函数库(I函数)
 * 使用方法:
 * <code>
 * input('id',0); 获取id参数 自动判断get或者post
 * input('post.name','','htmlspecialchars'); 获取$_POST['name']
 * input('get.'); 获取$_GET
 * input('param.test','','trim,htmlspecialchars,strip_tags',['',ENT_QUOTES,'']);//将单引号转换为html实体
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function input($name, $default = '', $filter = null, $datas = null)
{
    static $_PUT = null;
    if (strpos($name, '/')) { // 指定修饰符
        list($name, $type) = explode('/', $name, 2);
    } else { // 默认强制转换为字符串
        $type = 's';
    }
    if (strpos($name, '.')) { // 指定参数来源
        list($method, $name) = explode('.', $name, 2);
    } else { // 默认为自动判断
        $method = 'param';
    }
    switch (strtolower($method)) {
        case 'get'     :
            $input =& $_GET;
            break;
        case 'post'    :
            //payload消费方式
            
            $input = pre_save_des();
            //var_dump($input);exit();
            break;

        case 'put'     :
            if (is_null($_PUT)) {
                parse_str(file_get_contents('php://input'), $_PUT);
            }
            $input = $_PUT;
            break;
        case 'param'   :
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input = $_POST;
                    break;
                case 'PUT':
                    if (is_null($_PUT)) {
                        parse_str(file_get_contents('php://input'), $_PUT);
                    }
                    $input = $_PUT;
                    break;
                default:
                    $input = $_GET;
            }
            break;
        case 'path'    :
            $input = array();
            if (!empty($_SERVER['PATH_INFO'])) {
                $depr = '/';
                $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
            }
            break;
        case 'request' :
            $input =& $_REQUEST;
            break;
        case 'session' :
            $input =& $_SESSION;
            break;
        case 'cookie'  :
            $input =& $_COOKIE;
            break;
        case 'server'  :
            $input =& $_SERVER;
            break;
        case 'globals' :
            $input =& $GLOBALS;
            break;
        case 'data'    :
            $input =& $datas;
            break;
        default:
            return null;
    }
    if ('' == $name) { // 获取全部变量
        $data = $input;
        $filters = isset($filter) ? $filter : \Yaf\Registry::get('config')->user->default_filter;
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            foreach ($filters as $filter) {
                $data = array_map_recursive($filter, $data); // 参数过滤
            }
        }
    } elseif (isset($input[$name])) { // 取值操作
        $data = $input[$name];
        $filters = isset($filter) ? $filter : \Yaf\Registry::get('config')->user->default_filter;
        if ($filters) {
            if (is_string($filters)) {
                if (0 === strpos($filters, '/')) {
                    if (1 !== preg_match($filters, (string)$data)) {
                        // 支持正则验证
                        return isset($default) ? $default : null;
                    }
                } else {
                    $filters = explode(',', $filters);
                }
            } elseif (is_int($filters)) {
                $filters = array($filters);
            }

            if (is_array($filters)) {
                foreach ($filters as $filter) {
                    if (function_exists($filter)) {
                        if (!is_null($datas)) {
                            if (!empty(current($datas))) {
                                $datass = [$data, current($datas)];
                            }
                            next($datas);
                        }
                        $data = is_array($data) ? array_map_recursive($filter, $data) : call_user_func_array($filter, (isset($datass) ? $datass : [$data])); // 参数过滤
                    } else {
                        $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                        if (false === $data) {
                            return isset($default) ? $default : null;
                        }
                    }
                }
            }
        }
        if (!empty($type)) {
            switch (strtolower($type)) {
                case 'a':    // 数组
                    $data = (array)$data;
                    break;
                case 'd':    // 数字
                    $data = (int)$data;
                    break;
                case 'f':    // 浮点
                    $data = (float)$data;
                    break;
                case 'b':    // 布尔
                    $data = (boolean)$data;
                    break;
                case 's':   // 字符串
                default:
                    $data = (string)$data;
            }
        }
    } else { // 变量默认值
        $data = isset($default) ? $default : null;
    }
    is_array($data) && array_walk_recursive($data, 'other_safe_filter');
    return $data;
}

function pre_save_des(){
    //@$headers = getallheaders();
    //payload消费方式
    //application/x-www-form-urlencoded其实时json头
    $POST = json_decode(\Aes::decrypt(file_get_contents('php://input')),true);
    if(!is_null($POST)){
        return sizeof($POST) == 1 ? $POST[0] : $POST;
       
    }else{
        return $_POST;
    }
} 

/**
 * 其他安全过滤 From ThinkPHP 系统函数库 为input函数服务
 * @param $value
 */
function other_safe_filter(&$value)
{


    /*$filter = [
        'EXP','NEQ','GT','EGT','LT','ELT','OR','XOR','LIKE','NOTLIKE','NOT BETWEEN','NOTBETWEEN','BETWEEN','NOTIN','NOT IN','IN'
    ];*/
    //先替换特殊字符，再删除\,然后在对'和"进行转意
    //$value = htmlentities(stripslashes(sensitive($filter,$value)),ENT_QUOTES);
/*    $filter = [
        '>', '<', '\'', '"', '(', ')', 'select', 'delete', 'replace', 'update', 'drop', 'sleep', 'from', 'join', 'on'
    ];
    $value = stripslashes(sensitive($filter, $value));*/
    //$value = htmlentities(stripslashes($value),ENT_QUOTES);

}

/**
 * 用于input函数的递归
 * @param $filter
 * @param $data
 * @return array
 */
function array_map_recursive($filter, $data)
{
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
            ? array_map_recursive($filter, $val)
            : call_user_func($filter, $val);
    }
    return $result;
}

/**
 * 获取客户端IP地址 FROM ThinkPHP 系统函数库
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * GET 请求 FROM wechat-php-sdk
 * @param string $url
 */
function http_get($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 * GET 请求 FROM wechat-php-sdk
 * @param string $url
 */
function curl_get($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_HEADER, 0);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);

    if ($sContent === FALSE) {
        echo "CURL Error:" . curl_error($oCurl);
    }
    curl_close($oCurl);
    return array('content' => $sContent, 'info' => $aStatus);
}

/**
 * POST 请求 FROM wechat-php-sdk
 * @param string $url
 * @param array $param
 * @param boolean $post_file 是否文件上传
 * @return string content
 */
function http_post($url, $param, $post_file = false)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach ($param as $key => $val) {
            $aPOST[] = $key . "=" . urlencode($val);
        }
        $strPOST = join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}


/**
 * curl 批处理
 * @param $url_array
 * @return array
 * @author jsyzchenchen@gmail.com
 */
function curl_multi($data, $options = array())
{
    $handles = $contents = array();
    //初始化curl multi对象
    $mh = curl_multi_init();
    //添加curl 批处理会话
    foreach ($data as $key => $value) {
        $url = (is_array($value) && !empty($value['url'])) ? $value['url'] : $value;
        $handles[$key] = curl_init($url);
        curl_setopt($handles[$key], CURLOPT_RETURNTRANSFER, 1);

        //判断是否是post
        if (is_array($value)) {
            if (!empty($value['post'])) {
                curl_setopt($handles[$key], CURLOPT_POST, 1);
                curl_setopt($handles[$key], CURLOPT_POSTFIELDS, $value['post']);
            }
        }

        //extra options?
        if (!empty($options)) {
            curl_setopt_array($handles[$key], $options);
        }

        curl_multi_add_handle($mh, $handles[$key]);
    }
    //======================执行批处理句柄=================================
    $active = null;
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active and $mrc == CURLM_OK) {
        if (curl_multi_select($mh) === -1) {
            usleep(100);
        }
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
    //====================================================================
    //获取批处理内容
    foreach ($handles as $i => $ch) {
        $content = curl_multi_getcontent($ch);
        $contents[$i] = curl_errno($ch) == 0 ? $content : '';
    }
    //移除批处理句柄
    foreach ($handles as $ch) {
        curl_multi_remove_handle($mh, $ch);
    }
    //关闭批处理句柄
    curl_multi_close($mh);
    return $contents;
}

/**
 * mymd5 加盐的md5
 * @param string
 * @return string
 */
function mymd5($str)
{
    return md5($str . 'yafcms');
}

/**
 * getgid 获取用户组ID，如果有输入给数据库传递输入值，如果没有就传递session.group_id
 */
function getgid()
{
    $gid = input('gid', '');
    return empty($gid) ? $_SESSION['group_id'] : $gid;
}

/**
 *获得token
 */
function getToken()
{
    $data = [
        'id' => $_SESSION['uid'],
        'username' => $_SESSION['username'],
        'nickname' => $_SESSION['nickname'],
        'rid' => $_SESSION['rid'],
        'time' => time(),
    ];
    ksort($data);
    $string = http_build_query($data);
    $string = (new Aes())->encrypt($string);
    return $string;
}

/**
 * URL白名单，可没有签名直接通过，在这里配置
 */
if (!function_exists('whiteList')) {
    function whiteList()
    {
        return [
            //名称 => url
            'login' => '/Login/checklogin',
            'captchaimg' => '/Login/captchaimg',
            'menu' => '/menu',
        ];
    }
}
/**
 *解析token
 */
function parseToken()
{
    //url白名单过滤
    if (in_array($_SERVER['PATH_INFO'], whiteList())) {
        return TRUE;
    }

    //签名解析
    @$headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $Authorization = $headers['Authorization'];
    }
    if (isset($headers['authorization'])) {
        $Authorization = $headers['authorization'];
    }
    if (!isset($Authorization) or !empty($Authorization)) {
        parse_str((new Aes())->decrypt($Authorization), $result);
        return $result;
    }

    return FALSE;
}

/**
 * resultstatus 批处理
 * @param $url_array
 * @return json
 */
function resultstatus($code, $msg, $data = array(), $count = 0, $url = '')
{
    $result = array();
    $data = is_null($data) ? [] : $data;
    $result['errcode'] = (string)$code;
    $result['msg'] = $msg;
    $result['data'] = $data;
    $result['count'] = $count;
    $result['url'] = $url;
    return json_encode($result, JSON_UNESCAPED_UNICODE);
}


/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为true 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param integer $flags htmlspecialchars flags
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
{
    $label = (null === $label) ? '' : rtrim($label) . ':';
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
    $output = '<pre>' . $label . $output . '</pre>';
    echo($output);
}

if (!function_exists('build_select')) {

    /**
     * 生成下拉列表
     * @param string $name
     * @param mixed $options
     * @param mixed $selected
     * @param mixed $attr
     * @return string
     */
    function build_select($name, $options, $selected = [], $attr = [])
    {
        $options = is_array($options) ? $options : explode(',', $options);
        $selected = is_array($selected) ? $selected : explode(',', $selected);
        return Form::select($name, $options, $selected, $attr);
    }
}

if (!function_exists('build_radios')) {

    /**
     * 生成单选按钮组
     * @param string $name
     * @param array $list
     * @param mixed $selected
     * @return string
     */
    function build_radios($name, $list = [], $selected = null)
    {
        $html = [];
        $selected = is_null($selected) ? key($list) : $selected;
        $selected = is_array($selected) ? $selected : explode(',', $selected);
        foreach ($list as $k => $v) {
            $html[] = Form::radio($name, $k, in_array($k, $selected), ['title' => $v,]);
        }
        return implode("\n", $html);
    }
}

if (!function_exists('build_checkboxs')) {

    /**
     * 生成复选按钮组
     * @param string $name
     * @param array $list
     * @param mixed $selected
     * @return string
     */
    function build_checkboxs($name, $list = [], $selected = null)
    {
        $html = [];
        $selected = is_null($selected) ? [] : $selected;
        $selected = is_array($selected) ? $selected : explode(',', $selected);
        foreach ($list as $k => $v) {
            $html[] = Form::checkbox($name, $k, in_array($k, $selected), ['title' => $v, 'lay-skin' => 'primary']);
        }
        return implode("\n", $html);
    }
}


if (!function_exists('build_category_select')) {

    /**
     * 生成分类下拉列表框
     * @param string $name
     * @param string $type
     * @param mixed $selected
     * @param array $attr
     * @return string
     */
    function build_category_select($name, $type, $selected = null, $attr = [], $header = [])
    {
        $tree = Tree::instance();
        $tree->init(Category::getCategoryArray($type), 'pid');
        $categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = $header ? $header : [];
        foreach ($categorylist as $k => $v) {
            $categorydata[$v['id']] = $v['name'];
        }
        $attr = array_merge(['id' => "c-{$name}", 'class' => 'form-control selectpicker'], $attr);
        return build_select($name, $categorydata, $selected, $attr);
    }
}

//如果web服务器用的是nginx，则无法直接使用getallheaders()
if (!function_exists('getallheaders')) {
    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string[string] The HTTP header key/value pairs.
     */
    function getallheaders()
    {
        $headers = array();
        $copy_server = array(
            'CONTENT_TYPE' => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5' => 'Content-Md5',
        );
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }
        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        return $headers;
    }
}

//生成一个uuid
function uuid($format = 'int')
{
    $chars = md5(uniqid(mt_rand(), TRUE));

    if ($format == "dashes") {
        $uuid = substr($chars, 0, 8) . '-' . substr($chars, 8, 4) . '-' . substr($chars, 12, 4) . '-' . substr($chars, 16, 4) . '-' . substr($chars, 20, 12);
    } else {
        $uuid = strtoupper($chars);
    }

    return $uuid;
}

/**
 * 针对二维数组进行分组
 * $arr 要分组的数组
 * $key 分组的条件 如是$key一个数组,就去取他的value值作为分组显示标签，如果不是就只取key作为分组显示标签
 * $child 子数组是一个数组
 */
function array_group_by($arr, $key, $child = '')
{
    $grouped = array();
    foreach ($arr as $value) {
        if (empty($child)) {
            $grouped[$value[$key]][] = $value;
        } else {
            $grouped[$value[$key]]['key'] = $value[$key];
            $grouped[$value[$key]][$child][] = $value;
        }
    }
    if (func_num_args() > 3) {
        $args = func_get_args();
        foreach ($grouped as $key => $value) {
            $parms = array_merge($value, array_slice($args, 2, func_num_args()));
            $grouped[$key] = call_user_func_array('array_group_by', $parms, $child);
        }
    }
    sort($grouped);
    return $grouped;
}

/*
组织得到 terms elastics参数
$a 一维数组
$ip ip
return params string
*/
function gettermsparams($a, $ip = 'ip')
{
    $params = '{"query":  {"bool":  {"filter":  {"terms":  {"Device":  [';
    foreach ($a as $b) {
        if (!empty($b[$ip])) $params .= '"' . $b[$ip] . '",';
    }
    $params = substr($params, 0, strlen($params) - 1);
    $params .= ']}}}},"size":  0}';
    return $params;
}

/**
 * 从本地同步一个升级包到远端
 * $arr = [
 * host=>'19.19.19.19',
 * user=>'lin',
 * passwd=>'123456',
 * filename=>'upgrade.tar.gz'
 * ]
 */
function upgrade($arr = [])
{
    $command = 'ping -c 1 -w 1 ' . $arr['host'] . ' &>/dev/null && echo "ok" || echo "no"';
    exec($command, $res, $status);
    if ($res[0] == 'ok') {
        $command = "../shell/sync.expect {$arr['host']} {$arr['user']} {$arr['passwd']} {$arr['filename']}|tail -n 4";
        exec($command, $res1, $status);
    } else {
        return '网络故障';
    }
    if ($status === 0) {
        return resultstatus(200, '升级成功', implode('', $res1));
    } else {
        return resultstatus(400, '升级失败', implode('', $res1));
    }
}

/*function ping($ip,$times=4){
    echo $ip;
    $info = array();
    if(!is_numeric($times) ||  $times-4<0)
    {
        $times = 4;
    }
    if (PATH_SEPARATOR==':' || DIRECTORY_SEPARATOR=='/')//linux
    {
        echo 'linux';
        exec("ping $ip -c $times",$info);
        if (count($info) < 9)
        {
            $info['error']='timeout';
        }
    }
    else //windows
    {
        echo 'windows';
        exec("ping $ip -n $times",$info);
        if (count($info) < 10)
        {
            $info['error']='timeout';
        }
    }
   return $info;
    //var_dump($info);
}*/

/**
 * @abstract curl post请求  平台，采集器检测
 * @author 王成
 * @param $url 地址
 * @param $data  参数
 * @return mixed  返回信息
 */
function http_post_advertise($url, $data)
{//封装curl方法
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * @abstract curl post请求  升级平台
 * @author 王成
 * @param $url 地址
 * @param $data  参数
 * @return mixed  返回信息
 */
function file_post($url, $data)
{
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo;
}

/**
 * @abstract curl get请求  访问平台升级后重启操作
 * @author 王成
 * @param $url 地址
 * @param $data  参数
 * @return mixed  返回信息
 */
function file_get_restart($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
    $tmpInfo = curl_exec($curl);
    curl_close($curl);
    return $tmpInfo;
}

/**
 * @abstract  _json 格式化输出正常数据
 * @author    王晓辉
 * @param     array
 * @param     $msg  string
 * @return    Json
 */
function jsonResult($arr = [], $msg = '')
{

    $rs['errcode'] = '0';
    $rs['data'] = is_null($arr) ? [] : $arr;
    $rs['msg'] = $msg ? $msg : '成功';

    echo json_encode($rs, 256);
    exit();
}

/**
 * @abstract  _json 格式化输出错误数据
 * @author    王晓辉
 * @param     $arr  array
 * @param     $msg  string
 * @return    Json
 */
function jsonError($msg = '', $arr = [], $code = '1')
{

    $rs['errcode'] = $code;
    $rs['data'] = $arr;
    $rs['msg'] = $msg ? $msg : '失败';

    echo json_encode($rs, 256);
    exit();
}

/*拼装过滤数组
@parans $data 二维数组
@params $filter 保留一维数组 默认 sensor_id
return $string example ,1,2,3
*/
function getfilter($data, $filter = 'sensor_id')
{
    if (!empty($data)) {
        return implode('","', array_filter(array_unique(array_column($data, $filter)), function ($val) {
            if ($val !== '' && $val != null) return 1;
            return 0;
        }));
    }
    return false;
}

/*
从字符串中删除某一个值,或某一些值
@params string $string 1,2,3
@params string $val  1 删除的值或者 2,3
*/
function destroyval($string, $val)
{
    $tem = explode(',', $string);
    if (is_numeric($val)) {
        unset($tem[array_search($val, $tem)]);
        return implode(',', $tem);
    } else {
        $val = explode(',', $val);
        foreach ($val as $v) {
            unset($tem[array_search($v, $tem)]);
        }
        return implode(',', $tem);
    }
}

/**
 * @abstract 直接打印json字符串用
 */
function jsonEncode($params)
{
    echo json_encode($params);
    die;
}

/*拼装过滤数组
@parans $data 二维数组
@params $filter 保留一维数组 默认 sensor_id
return $string example ,[1,2,3]
*/
function array_col($data, $filter = 'es_id')
{
    if (!empty($data)) {
        return array_filter(array_unique(array_column($data, $filter)), function ($val) {
            if ($val !== '' && $val != null) return 1;
            return 0;
        });
    }
    return false;
}

/**
 * @todo 敏感词过滤，返回结果
 * @param array $list 定义敏感词一维数组
 * @param string $string 要过滤的内容
 * @return string $log 处理结果
 */
function sensitive($list, $string)
{
    $count = 0; //违规词的个数
    $sensitiveWord = '';  //违规词
    $stringAfter = $string;  //替换后的内容
    $pattern = "/" . implode("|", $list) . "/i"; //定义正则表达式
    if (preg_match_all($pattern, $string, $matches)) { //匹配到了结果
        $patternList = $matches[0];  //匹配到的数组
        $count = count($patternList);
        $sensitiveWord = implode(',', $patternList); //敏感词数组转字符串
        $replaceArray = array_combine($patternList, array_fill(0, count($patternList), '')); //把匹配到的数组进行合并，替换使用
        $stringAfter = strtr($string, $replaceArray); //结果替换
    }
    // $log = "原句为 [ {$string} ]<br/>";
    // if($count==0){
    //   $log .= "暂未匹配到敏感词！";
    // }else{
    //   $log .= "匹配到 [ {$count} ]个敏感词：[ {$sensitiveWord} ]<br/>".
    //     "替换后为：[ {$stringAfter} ]";
    // }
    // return $log;
    return $stringAfter;
}

/*
加密
 */
function encryptcode($input)
{

    return (new \Aes())->encrypt($input);
}

/*
 解密
 */
function decryptcode($input)
{

    return (new \Aes())->decrypt($input);
}

//获取毫秒
function getmicrotime($type = false)
{

    list($a, $b) = explode(" ", microtime());

    if ($type == true) {
        $rs = strval($b + sprintf('%.3f', $a));
    } else {
        $rs = date('Y-m-d H:i:s', $b) . '.' . strval(sprintf('%.3f', $a) * 1000);
    }

    return $rs;
}


/**
 * @Description: curl请求
 * @Author: Yang
 * @param $url
 * @param null $data
 * @param string $method
 * @param array $header
 * @param bool $https
 * @param int $timeout
 * @return mixed
 */
function curl_request($url, $data = null, $method = 'get', $header = array("content-type: application/json"), $https = true, $timeout = 5)
{
    // echo $url;die;
    $method = strtoupper($method);
    $ch = curl_init();//初始化
    curl_setopt($ch, CURLOPT_URL, $url);//访问的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//只获取页面内容，但不输出
    if ($https) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https请求 不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//https请求 不验证HOST
    }
    if ($method != "GET") {
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);//请求方式为post请求
        }
        if ($method == 'PUT' || strtoupper($method) == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//请求数据
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //模拟的header头
    //curl_setopt($ch, CURLOPT_HEADER, false);//设置不需要头信息
    $result = curl_exec($ch);//执行请求
    if (curl_errno($ch)) {
        echo 'Errno' . curl_error($ch);//捕抓异常
    }
    curl_close($ch);//关闭curl，释放资源
    return $result;
}


function geturl($url)
{
    $headerArray = array("Content-type:application/json;", "Accept:application/json");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    $output = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($output, true);
    return $output;
}


function posturl($url, $data)
{
    $data = json_encode($data);
    $headerArray = array("Content-type:application/json;charset='utf-8'", "Accept:application/json");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return json_decode($output, true);
}


function puturl($url, $data)
{
    $data = json_encode($data);
    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); //设置请求方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}

function delurl($url, $data)
{
    $data = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($output, true);
}

function patchurl($url, $data)
{
    $data = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);     //20170611修改接口，用/id的方式传递，直接写在url中了
    $output = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($output);
    return $output;
}

//是否为空
function checkHave($param)
{

        $param = decryptcode($param);
        if (!empty($param)) {
            return true;
        } else {
            return false;
        }


}

//是否是ip
function checkIp($ip)
{

        $ip = decryptcode($ip);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }


}

//是否符合长度
function checkLength($param)
{

        $param = decryptcode($param);
        if (strlen($param) > 150) {
            return false;
        } else {
            return true;
        }


}


//是不是整数
function checkerInt($param)
{

        $param = decryptcode($param);
        $params = $param * 10 % 10;

        if ($params != 0) {
            return false;
        } else {
            return true;
        }



}

//验证手机
function checkTel($param)
{

        $param = decryptcode($param);
        if (preg_match("/^1[3456789]\d{9}$/", $param)) {
            return true;
        } else {
            return false;
        }



}

//验证经度
function checkLon($param)
{

        $param = decryptcode($param);
        if (strpos($param, '-') !== false) {
            $lon = abs(intval($param));
            if ($lon > 180) {
                return false;
            } else {
                return true;
            }
        } else {
            $lon = intval($param);
            if ($lon > 180) {
                return false;
            } else {
                return true;
            }
        }



}

//验证纬度
function checkLat($param)
{

        $param = decryptcode($param);
        if (strpos($param, '-') !== false) {
            $lon = abs(intval($param));
            if ($lon > 90) {
                return false;
            } else {
                return true;
            }
        } else {
            $lon = intval($param);
            if ($lon > 90) {
                return false;
            } else {
                return true;
            }

    }

}

