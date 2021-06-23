#include <stdio.h>

int main()
{
	int a,b,c;
	a=30;
	b=20;

	c=(a>b);
	printf("c=%d\n",c);//1

	c=(a<b);
	printf("c=%d\n",c);//0

	c=(a=50);
	printf("c=%d\n",c);//50

	if(0) printf("18\n");
	if(1) printf("19\n");//19
	if(35) printf("20\n");//20
}