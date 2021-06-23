#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <dirent.h>



/*
	
	目录操作
	getcwd可以获取当前的工作目录
*/
//定义函数
int multLevelReadDir(const char *strpathname);

int main(int argc,char *argv[],char *envp[]){

	//单层目录遍历
	if(argc!=2){ printf("请指定目录名\n");return -1; }

	DIR *dir;//定义目录指针

	if((dir=opendir(argv[1])) == 0) return -1;

	//用于存放从目录中读取到的文件和目录信息
	struct dirent *stdinfo;

	while(1){
		//读取一行记录并且显示到屏幕中
		if((stdinfo=readdir(dir))==0) break;

		printf("name=%s,type=%d\n",stdinfo->d_name,stdinfo->d_type);
	}

	closedir(dir); //目录目录指针

	//多层目录遍历
	multLevelReadDir(argv[1]);
}

int multLevelReadDir(const char *strpathname){
	DIR *dir;//定义目录指针
	char strchdpath[512];//子目录的全路径

	if((dir=opendir(strpathname))==0) return -1;//打开目录
	
	struct dirent *stdinfo;//用于存放从目录读取到的文件和目录信息
	while(1){
		if((stdinfo=readdir(dir))==0) break;//读取一记录

		if(strncmp(stdinfo->d_name,".",1)==0) continue;//以.开始的文件不读

		if(stdinfo->d_type==8)//如果是文件，显示出来
			printf("%s/%s\n",strpathname,stdinfo->d_name);

		if(stdinfo->d_type==4){
			sprintf(strchdpath,"%s/%s",strpathname,stdinfo->d_name);
			multLevelReadDir(strchdpath);
		}
	}

	closedir(dir);//关闭目录指针

	return 0;
}

