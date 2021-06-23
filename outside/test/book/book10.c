#include <stdio.h>
#include <string.h>
#include <stdlib.h>

/*
	主函数的参数使用
*/
int main(int argc,char *argv[],char *envp[]){
	int i=0;
	printf("参数为%d\n",argc);

	for (i = 0; i < argc; ++i)
	{
		printf("参数%d,值为%s\n",i,argv[i]);
	}
	i=0;
	while(envp[i]!=0){//数组最后一个元素是0
		printf("会话变量:%s\n",envp[i]);i++;
	}
	//./book10 and or neq
}