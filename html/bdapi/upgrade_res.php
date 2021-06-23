<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/application/library/rsa.php';
$src = dirname(__FILE__);
$rsa = new Rsa();
exec("cat ".$src."/ustatus.txt",$output1);
exec("cat /version.txt",$output2);
$data['code'] = $output1[0];
$data['data'] = get_between($output2[0], 'V' , '-' );
echo $rsa->publicEncrypt(json_encode($data));
//截取版本号
function get_between($input, $start, $end) {
    $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
    return $substr;
}
?>