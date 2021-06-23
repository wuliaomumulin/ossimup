#include <stdio.h>

int main(){
	int value=0,count=0,sum=0;

	do
	{
		printf("请输入数字");
		scanf("%d",&value);

		sum += value;
		count++;


	} while(sum<5000);

	printf("您一共输入%d个数,和为%d\n",count,sum);
}