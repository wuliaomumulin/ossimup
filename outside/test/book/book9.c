#include <stdio.h>
#include <string.h>
#include <stdlib.h>

struct st_girl{
	char name[51];
	int age,height;
	double weight;
	char sc[31],yz[31];
};
//解析XML字符串的函数族
//reutrn 0:成功,-1:失败
int GetXMLBuffer_Int(const char *in_XMLBuffer,const char *in_FieldName,int *out_Value);
int GetXMLBuffer_Str(const char *in_XMLBuffer,const char *in_FieldName,char *out_Value);
int GetXMLBuffer_Double(const char *in_XMLBuffer,const char *in_FieldName,double *out_Value);

int main(){
	char str[301];memset(str,0,sizeof(str));
	//格式化输出到字符串中
	sprintf(str,"%d,%c,%lf,%s",10,'A',25.108,"一共输入了四个参数0");
	printf("%s\n",str);
	//截取前七个字符并且格式化输出到字符串中
	memset(str,0,sizeof(str));
	snprintf(str,50,"%d,%c,%lf,%s",10,'A',26.231,"一共输入了四个参数1");//第二个参数为字节长度，需要注意，在不同的编译器下会有不同的效果
	printf("%s\n",str);

	printf("xml解析");
	char xml[1024];memset(xml,0,sizeof(xml));	
	strcpy(xml,"<name>西施</name><age>20</age><height>178</height><weight>50.2</weight><sc>火辣</sc><yz>漂亮</yz>");

	printf("%s\n",xml);

	/**
	* 将一个结构体的数据格式化到一个字符串当中
	*/
	//初始化
	memset(xml,0,sizeof(xml));

	struct st_girl girl1;
	memset(&girl1,0,sizeof(struct st_girl));
	//赋值
	strcpy(girl1.name,"张三");
	girl1.age=28;
	girl1.height=170;
	girl1.weight=56.2;
	strcpy(girl1.sc,"普通");
	strcpy(girl1.yz,"一般");


	sprintf(xml,\
		"<name>%s</name><age>%d</age><height>%d</height><weight>%21lf</weight><sc>%s</sc><yz>%s</yz>",\
		girl1.name,girl1.age,girl1.height,girl1.weight,girl1.sc,girl1.yz
		);
	printf("%s\n",xml);

	/**
	* 将一个字符串解析到一个结构体中
	*/
	memset(&girl1,0,sizeof(struct st_girl));
	//解析
	GetXMLBuffer_Str(xml,"name",girl1.name);
	GetXMLBuffer_Int(xml,"age",&girl1.age);
	GetXMLBuffer_Int(xml,"height",&girl1.height);
	GetXMLBuffer_Double(xml,"weight",&girl1.weight);
	GetXMLBuffer_Str(xml,"sc",girl1.sc);
	GetXMLBuffer_Str(xml,"yz",girl1.yz);
	printf("姓名:%s,年龄:%d,身高:%d,体重:%lf,身材:%s,颜值:%s\n",girl1.name,girl1.age,girl1.height,girl1.weight,girl1.sc,girl1.yz);


}

int GetXMLBuffer_Int(const char *in_XMLBuffer,const char *in_FieldName,int *out_Value){
	char strvalue[51];
	memset(strvalue,0,sizeof(strvalue));
	if(GetXMLBuffer_Str(in_XMLBuffer,in_FieldName,strvalue)!=0) return -1;

	(*out_Value)=atoi(strvalue);
	return 0;

};
int GetXMLBuffer_Str(const char *in_XMLBuffer,const char *in_FieldName,char *out_Value){
	if(out_Value==0) return -1; //如果是空指针，则返回失败

	char *start=0,*end=0;
	char m_SFieldName[51],m_EFieldName[51];//字段的开始和结束标签
	int m_NameLen = strlen(in_FieldName);//字段名长度
	memset(m_SFieldName,0,sizeof(m_SFieldName));
	memset(m_EFieldName,0,sizeof(m_EFieldName));
	snprintf(m_SFieldName,50,"<%s>",in_FieldName);
	snprintf(m_EFieldName,50,"</%s>",in_FieldName);
	start=0;end=0;
	start=(char *)strstr(in_XMLBuffer,m_SFieldName);//字段开始标签的地方
	if(start!=0){
		end = (char *)strstr(in_XMLBuffer,m_EFieldName);//字段结束的标签
	}
	if(start==0||end==0) return -1;
	int m_ValueLen = end - start - m_NameLen - 2;//字段值的长度
	strncpy(out_Value,start+m_NameLen+2,m_ValueLen);//获取字段的值
	out_Value[m_ValueLen] = 0;
	return 0;

};
int GetXMLBuffer_Double(const char *in_XMLBuffer,const char *in_FieldName,double *out_Value){
	char strvalue[51];
	memset(strvalue,0,sizeof(strvalue));
	if(GetXMLBuffer_Str(in_XMLBuffer,in_FieldName,strvalue)!=0) return -1;

	(*out_Value)=atof(strvalue);
	return 0;
};