#include <stdio.h>
#include <string.h>

int main(){

	int sum[5];//定义一维数组
	for(int i=0;i<100;i++){
		printf("%d\n",i);
		sum[i]=i;
	}

	printf("----------------一维数组的循环-------------\n");

	//在循环中使用数组
	int total=5;//循环计数器，总人数
	double weight[total],sum1=0;//全体体重数据和总和


	memset(weight,0,sizeof(weight));//初始化数组为0

	for (int i = 0; i < total; ++i)
	{
		printf("请输入第%d名顾客的体重:",i+1);
		scanf("%lf",&weight[i]);
		sum1+=weight[i];
	}

	printf("顾客的平均体重为%lf\n",sum1/total);

	
	//二维数组的使用,如有问题可能是C本身的double问题，或是g++的问题
	
	printf("----------------二维数组的使用-------------\n");

	int class=3;total=5;
	double price[class][total],sum2[class];
	//采用双层循环
	for (int i = 0; i < class; ++i)
	{
		for (int j = 0; j < total; ++j)
		{
			printf("请输入第%d桌的%d号顾客",i+1,j+1);
			scanf("%lf",&price[i][j]);
			sum2[i]+=price[i][j];
		}
		
		printf("第%d桌顾客的平均餐费为:%lf / %d = %lf\n",i+1,sum2[i],total,sum2[i]/total);

	}

	printf("----------------字符串数组和字符数组-------------\n");

	char strname[22];//可以存放二十个字符串
	memset(strname,0,sizeof strname); 
	strcpy(strname,"我确定可以存");//char 1 byte | 汉字 3 byte | int 4 byte | double 8 byte
	printf("%s\n",strname);

	char strname1[10][37];//10个字符串，每个字符串可以存20个字符
	memset(strname1,0,sizeof strname1);

	for(int i=0;i<sizeof strname1;i++){
		strcpy(strname1[i],"数字和字符串不可隐形转换");
		printf("您输入的是:%s--",strname1[i]);
	}

	return 0;

}