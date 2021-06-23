<?php
/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/
require_once dirname(dirname(dirname(__FILE__))).'/application/library/rsa.php';

ini_set('max_execution_time', '0');
date_default_timezone_set("PRC");

$src = dirname(__FILE__);

$rsa = new Rsa();
$privEncrypt1 = $_POST['params'];

if($privEncrypt1){
    $data = json_decode(gzuncompress($rsa->publicDecrypt($privEncrypt1)), 256);
  // var_dump($data);die;
    if($data != null ){
        exec("echo  \"接收升级通知|".$privEncrypt1."|".date('Y-m-d H:i:s')."\" >>".$src."/upgrade_log.txt");
        exec("cat ".$src."/ustatus.txt", $outp);
        $res = json_decode($outp[0]);
        if (is_object($res) && $res->status == 2) {
            if ($res->start_time > time() - 1800) {
                exec("echo  \"升级中不能再升级,通知到此结束|".date('Y-m-d H:i:s')." \">>".$src."/upgrade_log.txt");
                exit();
            }else{
                exec("echo  \"升级超时,再次发起升级请求|".date('Y-m-d H:i:s')." \">>".$src."/upgrade_log.txt");
            }
        }

        $privEncrypt2 = $_POST['file_hash'];
        $file_hash = json_decode($rsa->publicDecrypt($privEncrypt2), 256);
        //获取当前版本比较
        $new_version_num = $data['version'];
        if (substr($new_version_num, 0, 1) == 'V' || substr($new_version_num, 0, 1) == 'v') {
            $new_version_num = substr($new_version_num, 1);
        } else {
            $new_version_num = $data['version'];
        }

        exec("cat /version.txt", $output);
        if ($output) {
            $now_version_num = get_between($output[0], 'V', '-');

            $result = versionCompare($new_version_num, $now_version_num);//输出结果为1
            if ($result == 1 || ($result == -1 && $data['is_upgrade'] == 1)) {
                //下载升级包
                exec('echo {\"status\":2,\"start_time\":' . time() . '} > '.$src.'/ustatus.txt');

                exec("curl -k -C - -o ".$src."/upgrade/upgrade.zip https://".$data['file_src']);

                //判断升级包是否下载完成
                if (file_exists($src."/upgrade/upgrade.zip")){

                    //hash是否一致
                    if ($file_hash == md5_file($src.'/upgrade/upgrade.zip')) {
                        //完成后进行升级
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, 'http://localhost:8080/interface/edit?type=upgrade&file='.$src.'/upgrade/upgrade.zip');
                        curl_setopt($curl, CURLOPT_HEADER, 0);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        $data = curl_exec($curl);
                        curl_close($curl);
                        unlink($src.'/upgrade/upgrade.zip');
                    }else{
                        exec("echo  \"文件下载错误，不是原文件|".date('Y-m-d H:i:s')."\" >>".$src."/upgrade_log.txt");
                    }
                }else{
                    exec("echo  \"文件下载未完成|".date('Y-m-d H:i:s')."\" >>".$src."/upgrade_log.txt");
                }

            }elseif($result == 0){
                exec("echo  \"已是最新版本，无需升级|".date('Y-m-d H:i:s')."\" >>".$src."/upgrade_log.txt");
            }elseif($result == -1 && $data['is_upgrade'] == 0){
                exec("echo  \"升级版本小于当前版本，无强制升级，无需升级|".date('Y-m-d H:i:s')."\" >>".$src."/upgrade_log.txt");
            }

        }
    }

}else{
    exec("echo  \"未接收升级通知|".date('Y-m-d H:i:s')."\" >>".$src."/upgrade_log.txt");
}


//截取版本号
function get_between($input, $start, $end)
{
    $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
    return $substr;
}

//正则提取字符串中的数字
function reg($str)
{
    return preg_replace('/[^0-9]/', '', $str);
}

//根据length的长度进行补0的操作，$length的值为两个版本号中最长的那个
function add($str, $length)
{
    return str_pad($str, $length, "0");
}

//实现逻辑
function versionCompare($v1, $v2)
{
    $length = strlen(reg($v1)) > strlen(reg($v2)) ? strlen(reg($v1)) : strlen(reg($v2));
    $v1 = add(reg($v1), $length);
    $v2 = add(reg($v2), $length);
    if ($v1 == $v2) {
        return 0;
    } else {
        return $v1 > $v2 ? 1 : -1;
    }
}

?>