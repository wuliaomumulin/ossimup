<?php

header("Content-type: text/html; charset=utf-8");    
 
/**
* 将字符串转换成二进制
* @param type $str
* @return type
*/
function StrToBin($str){
    //1.列出每个字符
    $arr = preg_split('/(?<!^)(?!$)/u', $str);
    //2.unpack字符
    foreach($arr as &$v){
        $temp = unpack('H*', $v);
        $v = base_convert($temp[1], 16, 2);
        unset($temp);
    }
 
    return join(' ',$arr);
}
 
/**
* 讲二进制转换成字符串
* @param type $str
* @return type
*/
function BinToStr($str){
    $arr = explode(' ', $str);
    foreach($arr as &$v){
        $v = pack("H".strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
    }
 
    return join('', $arr);
}
 
echo StrToBin("php二次开发：www.php2.cc");;
echo '<br/>';
echo BinToStr("1110000 1101000 1110000 111001001011101010001100 111001101010110010100001 111001011011110010000000 111001011000111110010001 111011111011110010011010 1110111 1110111 1110111 101110 1110000 1101000 1110000 110010 101110 1100011 1100011");