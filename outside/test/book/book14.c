#include <stdio.h>
#include <string.h>
#include <time.h>
#include <unistd.h>


/*
	时间操作


*/

//该结构体定义在time.h头文件中
//判断一个宏是否被定义

//#ifdef  _TIME_H
//__BEGIN_NAMESPACE_STD
/* Used by other time functions.  */

//struct tm {
//        int tm_sec;             /* Seconds.  秒  [0-60] (1 leap second) */
//        int tm_min;             /* Minutes.  分  [0-59] */
//        int tm_hour;            /* Hours.   时    [0-23] */
//        int tm_mday;            /* Day.   日期:一个月中的日期，其值区间为      [1-31] */
//        int tm_mon;             /* Month.  月份，从一月开始，0代表一月，其值区间为     [0-11] */
//        int tm_year;            /* Year - 1900. 年份，其值等于实际年份减去1900  */ 
//        int tm_wday;            /* Day of week. 星期，0代表星期天,1代表星期1，其值区间为 [0-6] */
//        int tm_yday;            /* Days in year.从每年的1月1日开始的天数,0代表1月1日，1代表1月2日，以此类推 [0-365] */
//        int tm_isdst;           /* DST. 夏时令标识符   [-1/0/1] */

//#ifdef  __USE_MISC
//        long int tm_gmtoff;     /* Seconds east of UTC.  */
//        const char *tm_zone;    /* Timezone abbreviation.  */
//#else
//        long int __tm_gmtoff;   /* Seconds east of UTC.  */
//        const char *__tm_zone;  /* Timezone abbreviation.  */
//#endif
//};

struct timeval{
	long tv_sec;
	long tv_user;
};

int main(int argc,char *argv[],char *envp[]){
	//多层目录遍历
	time_t tnow;
	tnow = time(0);//获取当前时间
	printf("tnow=%d\n",tnow);

	//localtime库函数
	struct tm *sttm;
	sttm = localtime(&tnow);//把整数的时间转换为struct tm结构体的时间
	//yyyy-mm-dd hh24:mi:ss格式输出，此格式用的最多
	printf("%04u-%02u-%02u %02u:%02u:%02u\n",sttm->tm_year+1900,\
		sttm->tm_mon+1,\
		sttm->tm_mday,\
		sttm->tm_hour,\
		sttm->tm_min,\
		sttm->tm_sec\
		);
	printf("%04u年%02u月%02u日 %02u时%02u分%02u秒\n",sttm->tm_year+1900,\
		sttm->tm_mon+1,\
		sttm->tm_mday,\
		sttm->tm_hour,\
		sttm->tm_min,\
		sttm->tm_sec\
		);

	printf("%04u-%02u-%02u\n",sttm->tm_year+1900,\
		sttm->tm_mon+1,\
		sttm->tm_mday\
		);

	//mktime库函数与之相反
	printf("时间为:%lu\n",mktime(sttm));

	
	/*
		gettimeofday是互动二当前秒和微妙的时间，其中的秒是指的1970-1-1到现在的秒，微妙是指当前秒已逝去的微秒数，可以用来表示当前的时间

		struct timeval{
			long tv_sec;//从1970-1-1到现在的秒
			long tv_usec;//当前秒的微妙，即百万分之一秒
		}
	*/
	struct timeval begin,end;//用于存放开始和结束时间
	gettimeofday(&begin,0);//计时器开始
	printf("开始时间为:%d,tv_sec:%d,tv_userc:%d\n",time(0),begin.tv_sec,begin.tv_user);

	//程序睡眠
	sleep(2);//睡眠1秒
	usleep(100000);//秒睡十分之一秒

	gettimeofday(&end,0);//计时器开始
	printf("结束时间为:%d,tv_sec:%d,tv_userc:%d\n",time(0),end.tv_sec,end.tv_user);

	printf("计时过去了%d微秒。\n",(end.tv_sec-begin.tv_sec)*1000000+(end.tv_user-begin.tv_user));

}