#!/bin/bash

# liyb
  
mysql -uroot -pAdmin@123 -e "select version()"
if [ $? -ne 0 ]
then
# mysql出现问题 

 # mysqldump导入数据库脚本
 # gunzip < alldb1.sql.gz | mysqldump -uroot -p123456 test
 
 # 复制mysql数据库
 rm -rf /var/lib/mysql
 cp -r /work/web/databases/mysql_backup /var/lib/mysql
 chown -R mysql:mysql /var/lib/mysql
 
 echo "恢复mysql中"
 # mysql启动的几种方式
 # /etc/init.d/mysql start
 systemctl start mysql 

else
 # 备份数据库
 mkdir -p /work/web/databases
 rm -rf /work/web/databases/*

 mysqldump -uroot -pAdmin@123 --all-databases | gzip  > /work/web/databases/all_databases.sql.gz
 cp -r /var/lib/mysql /work/web/databases/mysql_backup

 echo "mysql running"
fi
