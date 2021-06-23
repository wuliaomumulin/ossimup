#include <stdio.h>
#include <string.h>
#include <stdlib.h>

//用typedef 来定义结构体的别名
typedef struct st_girl{
	char name[50];
	int age;
} st_girl;
/*
	动态内存管理
*/
int main(int argc,char *argv[],char *envp[]){
	//printf("%d\n", sizeof(int));//4
	//printf("%d\n", sizeof(long));//8
	//printf("%d\n", sizeof(double));//8
	int *pi=malloc(sizeof(int));//分配四个字节的空间
	long *pl=malloc(sizeof(long));
	double *pd=malloc(sizeof(double));
	char *pc=malloc(101);//分配101个字节的内容，可存放100个字符的字符串
	st_girl *pst=malloc(sizeof(st_girl));//分配结构体大小的内存
	//以下是像普通指针和变量一样操作动态分配的内存
	*pi=10;*pl=201;*pd=24.4;strcpy(pc,"东邪");strcpy(pst->name,"结构体");pst->age=26;
	printf("pi=%d,pl=%ld,pd=%lf,pc=%s,st_girl->name=%s,st_girl.age=%d\n",*pi,*pl,*pd,pc,pst->name,pst->age);
	free(pi);free(pl);free(pd);free(pc);free(pst);

}