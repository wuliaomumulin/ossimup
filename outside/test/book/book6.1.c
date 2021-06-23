#include "book6.1.h" //包含自定义文件头的声明

//声明min函数，用于比较两个整数的大小，取小者
int min(const int i,const int j){
	if(i<j) return i;
	return j;
}
//声明min函数，用于比较两个整数的大小，取小者
int max(const int i,const int j){
	if(i>j) return i;
	return j;
}
