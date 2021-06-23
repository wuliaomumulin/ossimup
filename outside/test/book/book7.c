#include "public.h"

int main()
{
	char a='E',b='F';
	printf("字符为%c,数字为%d\n请输入一个字符:",a,a+b);	

	char c;
	scanf("%c",&c);
	//
	printf("'%c'用ascii字符进行转换为数字%d\n",c,ctoi(c));

}