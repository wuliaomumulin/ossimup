#include <stdio.h>
#include <string.h>
#include <stdlib.h>



/*
	*fopen(const char *pathname, const char *mode);

	 文件操作
	 r 只读 文件必须存在，否则打开失败
	 w 只写 如文件存在则清除文件内容，如果文件不存在，则新建文件
	 a 追加只写 如果文件存在，则打开文件，如果文件不存在，则新建文件
	 r+ 读写 文件必须存在，在只读r的基础上加'+'表示增加可写的操作
	 w+ 读写 在只写w的方式上增加可读的操作
	 a+ 读写 在追加只写a的方式上增加可读的操作
*/

struct st_girl{
	char name[51];
	int age,height;
	double weight;
	char sc[31],yz[31];
};

int main(int argc,char *argv[],char *envp[]){

	struct st_girl girl1;//定义结构体变量
	struct st_girl girl2;//定义结构体变量
	FILE *fp = 0;
	char url[31];
	memset(url,0,sizeof(url));
	strcpy(url,"/tmp/test1.dat");
	//以只写的方式打开文件
	if((fp = fopen(url,"w")) == 0){
		printf("文件%s写入失败\n",url);return -1;
	}
	//文件写入
	strcpy(girl1.name,"张三");girl1.age=27;girl1.height=199;girl1.weight=70.2;strcpy(girl1.sc,"一般");strcpy(girl1.yz,"牛皮");
	fwrite(&girl1,1,sizeof(girl1),fp);
	strcpy(girl1.name,"李四");girl1.age=21;girl1.height=166;girl1.weight=76.7;strcpy(girl1.sc,"二般");strcpy(girl1.yz,"大度");
	fwrite(&girl1,1,sizeof(girl1),fp);
	
	//关闭文件指针
	fclose(fp);

	if((fp = fopen(url,"rb")) == 0){
		printf("文件%s打开失败\n",url);return -1;
	}

	//文件读取
	while(1){
		//从文件中读取数据，存入数据结构变量中
		if(fread(&girl2,1,sizeof(struct st_girl),fp) == 0) break;
		//显示数据
		printf("姓名:%s,年龄:%d,身高:%d,体重:%lf,身材:%s,颜值:%s\n",girl2.name,girl2.age,girl2.height,girl2.weight,girl2.sc,girl2.yz);
	}

	/*
		文件指针定位
		ftell(FILE *fp) 返回当前文件位置指针的值，这个值是当前位置相对于文件开始位置的字节数
		rewind(FILE *fp) 移动文件指针到开头
		fseek(FILE *fp,long offset,int origin) offset偏移量，正数向右移动，负数向左移动，origin起始位置，C语言有三个值，0-文件开头，1-当前位置，2-文件末尾
	*/
	

	//关闭文件指针
	fclose(fp);
}