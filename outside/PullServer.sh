#!/bin/bash
# liyb
# 服务拉起脚本

# 	每10分钟拉起服务脚本走一次，运行一下下面一句，将服务注入计划任务
# 	sed -i '$a */10 * * * * /bin/bash /work/web/outside/PullServer.sh > /dev/null 2>&1' /var/spool/cron/crontabs/root

# apache
apache_recover(){
	nums=$(ss -nltp|grep apache2|wc -l)
	if [[ $nums -eq 0 ]];then
		/etc/init.d/apache2 restart
	fi
}

# elasticsearch
elasticsearch_recover(){
	nums=$(ss -nltp|grep 9200|wc -l)
	if [[ $nums -eq 0 ]];then
		/etc/init.d/elasticsearch restart  
	fi
}

# mysql
mysql_recover(){
	nums=$(ss -nltp|grep mysqld|wc -l)
	if [[ $nums -eq 0 ]];then
		/etc/init.d/mysql restart

		# 再次检测启动是否正常
		num=$(ss -nltp|grep mysqld|wc -l)
		if [[ $num -eq 0 ]];then
			# 如果依然无法启动，则尝试数据恢复工作
			rsync -ar /var/tokudb/backup/mysql_data_dir/ /var/lib/mysql/
			/etc/init.d/mysql restart
		fi

	fi
}

# redis
redis_recover(){
	nums=$(ss -nltp|grep redis-server|wc -l)
	if [[ $nums -eq 0 ]];then
		/etc/init.d/redis-server restart 
	fi
}



nums=$(ss -nltp|egrep 'mysqld|apache2|redis-server|9200'|wc -l)
if [[ $nums -lt 4 ]];then

	# 说明服务不正常
	apache_recover
	elasticsearch_recover
	mysql_recover
	redis_recover

	exit 0
# else
	# 机器健康,什么也不做
	# echo "health"

fi

