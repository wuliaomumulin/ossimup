#include "public.h" //包含自定义文件头的声明

/**
* 将字符的0-9转化成数字的0-9
* 0 => 48
* 1 => 49
* 2 => 50
*/
int ctoi(const char chr){
	return chr-48;
}