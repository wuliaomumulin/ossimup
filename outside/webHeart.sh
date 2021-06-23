#!/bin/bash
# liyb


url=127.0.0.1/api.php

sta=$(curl -s -m 5 -IL ${url}|grep 200)
if [ "$sta" == "" ];then
	systemctl restart apache2
	systemctl restart mysql
	systemctl restart redis
	echo "server restart success"

	# 再此尝试
fi
