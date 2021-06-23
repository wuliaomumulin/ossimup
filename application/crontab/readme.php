<?php

$x = -123;


/*
echo pow(2,32);
exit();*/

function aa($x){
	$a = 0;
	$f = $x < 0 ? '-' :'';
	$t = abs($x);
	while ($t > 0) {
		$a = $a*10+($t%10);
		$t = floor($t/10);
	}
	if(!empty($f)){

		$a=$f.$a;
	}
	$a = (int)$a;

	//加入条件
	if($a > (pow(2,31)-1) || $a < pow(-2,31)){
		return 0;
	}

	return $a;
}

//IV
/**
* 当小值在大值的左边，则减小值，如IV=5-1=4
* 当小值在大值的右边，则加小值，如VI=5+1=6
* 
*/

function romanToInt(string $s){
	$sum = 0;
	$pre = formatNum($s[0]);
	$len = strlen($s);


	for ($i=1; $i < $len; $i++) { 
		$num = formatNum($s[$i]);
		if ($pre < $num){
			$sum-=$pre;
		}else{
			$sum+=$pre;
		}
		$pre = $num;
	}

	$sum += $pre;
	return $sum;

}


function formatNum(string $s){
	switch ($s) {
		case 'I':
			return 1;
			break;
		case 'V':
			return 5;
			break;
		case 'X':
			return 10;
			break;
		case 'L':
			return 50;
			break;
		case 'C':
			return 100;
			break;
		case 'D':
			return 500;
			break;
		case 'M':
			return 1000;
			break;
		default:
			return 0;
			break;
	}
}
//I V X L C D M
echo romanToInt('LIV');

//echo aa($x);

?>