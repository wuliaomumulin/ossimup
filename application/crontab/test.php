<?php


    function hex($str){
        return bin2hex($str);
    }


    function unhex($str){
    	$arr = str_split($str,2);
    	$val = '';
    	foreach ($arr as $v) {
    		$val .= hex2bin((int)$v);
    	}
        return $val;
    }

## 测试用例
/* 
	不是大端就是小端，每两个字符为一组,第一位为高位，即大端，否则小端
*/
var_dump($a = pack('H*','16D2E6F67D343B87549A1E3964B6449A'));
var_dump(unpack('H*',$a)[1]);

$b = pack('H*','17D8BACFFEE4E3B4E533EF9C08C00EC5');
$c = pack('H*','085D1C4AD4D2C84157AC9FB855EE5993');

//ar_dump($b,$c);exit();

$pdo = new PDO('mysql:host=19.19.19.70;dbname=test;charset=utf8','root','123456');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);//设置筛选模式

$sql = "insert into `test_bin` values('{$a}'),('{$b}'),('{$c}')";

$num = $pdo->exec($sql);

var_dump($num);
//还有一个问题，就是return binary,没有办法表示二进制


/*$input = 'ni and1';
echo substr($input,-3)=='and' ? 'yes' : 'no';*/
exit();

/*

CIDR方法计算

*/
//$ip="19.19.19.0/15";
//print_r(unpack("L",$ip));
$ip = '19.18.0.0/14';
// echo ip2long($ip).PHP_EOL;
// echo ip2long($ip)&ip2long("255.254.0.0");



//ip2long long2ip

// $ret = vaildation_network($ip);
// var_dump($ret);
// 19.19.19.0/15

// 19.19.19.0
// 00010011 00010011 00010011 00000000

// 255.254.0.0
// 11111111 11111110 00000000 00000000

// 相与
// 00010011 00010010 00000000 00000000







$str = '19.19.32.0/21';

if(!preg_match("/^(?:(?:[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}(?:[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\/([1-9]|[1-2]\d|3[0-2])$/",$str,$matches)){
    echo "no";
 }
var_dump($matches);

//pat = "((25[0-5]|2[0-4]\d|[01]\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]\d\d?)";

exit;



function longestCommandPrefix($strs){
	$n = count($strs);
	if($n == 0) return '';
	if($n == 1) return reset($strs);
	$ans = '';

	$firstStrLen = strlen(reset($strs));
	for($k = 0;$k < $firstStrLen;++$k){
		$c = substr($strs[0],$k,1);
		for($i=1; $i < $n; ++$i){ 
			$contrast = substr($strs[$i],$k,1);
			if($contrast === false || $contrast != $c){
				echo 'if -- ans is '.$ans.',constract is '.$contrast.',c is'.$c.PHP_EOL;
				return $ans;
			}
			echo 'else -- ans is '.$ans.',constract is '.$contrast.',c is '.$c.PHP_EOL; 
		}
		$ans .= $c; 
		echo $ans.'一层'.PHP_EOL;
	}

	return $ans;

}

function isValid($s){
	$length = strlen($s);

	if($length % 2 == 1){
		return false;
	}
	
	$m = [')'=>'(',']'=>'[','}'=>'{'];
	$stack = new SplStack();

	for ($i=0; $i < $length; $i++) { 
		
		if(!array_key_exists($s[$i],$m)){
			$stack->push($s[$i]);
		}else{

			if(!$stack->isEmpty() && $m[$s[$i]] == $stack->top()){
				$stack->pop();
			}else{

				return false;
			}

		}	
	}

	return $stack->isEmpty();
	
}






// &按位与运算
// echo (int)"g" & 1;

var_dump(isValid('(('));
var_dump(isValid('()'));
var_dump(isValid('(]'));
var_dump(isValid('{[]}'));
var_dump(isValid('([)]'));
var_dump(isValid('()[]{}'));

//echo longestCommandPrefix(['flower','flow','flight','x']);

/*

The following dependencies are needed and will be installed: 

- build-essential
- autoconf
- mesa-utils
- unzip
- apt-file
*/


?>