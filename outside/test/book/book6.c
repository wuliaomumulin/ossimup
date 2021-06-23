#include "book6.1.h" //包含自定义文件头的声明

//声明一个函数
//gcc -o book6 book6.c book6.1.c 编译运行
int checksc(int number);

int main()
{
	int i,j;
	printf("请输入一个10-40的数字:");
	scanf("%d",&i);

	j=checksc(i);
	switch(j){
		case 1:printf("小\n");break;
		case 2:printf("中\n");break;
		case 3:printf("大\n");break;
		default:printf("不合格\n");break;
	}

	printf("--------引入公共文件--------------\n");
	printf("请输入两个数:(以空格相隔)");
	scanf("%d %d",&i,&j);
	printf("小值为%d,大值为%d\n",min(i,j),max(i,j));
}

//定义一个函数
int checksc(int number){
	if(number>=10 && number<=20) return 1;
	if(number>=20 && number<=30) return 2;
	if(number>=30 && number<=40) return 3;

	return 0;
}