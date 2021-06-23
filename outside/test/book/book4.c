#include <stdio.h>

int main(){
	int sum=0;double aa=0;
	for(int i=1;i<=100;i++){
		sum+=i;
	};

	printf("1-100累加的值为%d\n",sum);

	printf("请输入一个double类型的小数");
	scanf("%lf",&aa);
	printf("您输入的小数为%lf\n",aa);
}