#include <stdio.h>
#include <string.h>

struct st_girl
{
	char name[50];
	int age;
	int height;
	char sc[30],yz[20];//身材，颜值
};

struct st_man{
 char name[50];
 int age;
};
//对结构体赋值的函数
void setStMan(struct st_man *pst);

int main(){

	//第一种
	struct st_girl queen; //定义结构体变量
	memset(&queen,0,sizeof(struct st_girl));//初始化结构体变量

	strcpy(queen.name,"张三");
	queen.age = 29;
	queen.height = 187;
	strcpy(queen.sc,"高大");
	strcpy(queen.yz,"威猛");

	printf("姓名:%s,年龄:%d岁,身高:%dcm,身材:%s,颜值:%s\n",queen.name,queen.age,queen.height,queen.sc,queen.yz);

	//第二种
	struct st_man *pst,man;//定义结构体变量
	memset(&man,0,sizeof(struct st_man));//初始化结构体变量
	pst=&man;
	//对每个结构体变量赋值
	strcpy(pst->name,"李四");
	pst->age = 25;
	printf("姓名:%s,年龄:%d\n",man.name,man.age);
	printf("姓名:%s,年龄:%d\n",pst->name,pst->age);
	printf("姓名:%s,年龄:%d\n",(*pst).name,(*pst).age);

	/*
		结构体赋值
		1、结构体成员逐个赋值给另一个成员变量
		2、内存拷贝;(memcpy)
	*/
	struct st_man man1,man2;
	strcpy(man1.name,"王五");
	man1.age = 27;
	//把man1的内容复制给man2
	memcpy(&man2,&man1,sizeof(struct st_man));
	printf("man1的姓名为:%s,年龄为:%d\n",man1.name,man1.age);
	printf("man2的姓名为:%s,年龄为:%d\n",man2.name,man2.age);

	/*
		结构体作为函数的参数
	*/
	struct st_man man3;
	memset(&man3,0,sizeof(struct st_man));//初始化结构体

	setStMan(&man3);
	printf("姓名:%s,年龄:%d\n",man3.name,man3.age);

	/**
	* 枚举和共同体应用很少，不做举例
	*/

}
//对结构体赋值的函数实体
void setStMan(struct st_man *pst){
	//对每个结构体成员赋值
	strcpy(pst->name,"赵六");
	pst->age = 28;
}