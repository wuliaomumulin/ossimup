# shell学习笔记
 
## 一、
## 二、
## 三、
## 四、
## 五、
## 六、
## 七、
## 八、
## 九、














## 十、数组及函数编程
### 一维数组
```
A=(test1 test2 test3)
echo ${A[0]}
echo ${A[1]}
echo ${A[@]} # 显示所有参数test1 test2 test3
echo ${#A[@]} # 显示数组个数
```
example
```
#!/bin/bash

soft = (
	nginx-1.6.1.tar.gz
	mysql-5.1.17.tar.gz
	php-7.2.28.tar.gz
	etc/test/sysctl.conf
)
```

### 函数
```
#!/bin/bash

NGX_FILES=nginx-1.6.1.tar.gz
DWN_URL=http://nginx.org/download
MYSQL_FILES=mysql-5.1.17.tar.gz

function nginx_install(){
	wget -c ${DWN_URL}/${NGX_FILES}
	tar xzf ${NGX_FILES}
}
```




## 十一、
## 十二、
## 十三、
## 十四、
## 十五、
## 十六、
## 十七、
## 十八、
## 十九、
## 二十、
## 二十一、

## mysql压缩备份与还原
```
mysqldump -uroot -p123456 -B alienvault alienvault_api alienvault_asec alienvault_siem datawarehouse ossim_acl osvdb PCI ISO27001An PCI3 | gzip > /work/install/mysql-packet/alldb1.sql.gz
gunzip < alldb1.sql.gz | mysqldump -uroot -p123456 test3

```

rm -rf `ls /root/20200730/test/ | egrep -v '(mysql|performance_schema|test)'`


网卡的启停
ifconfig eth4 192.168.1.105 netmask 255.255.255.0 up
ip addr del 192.168.1.105 dev eth4
ifconfig eth4 up|down


apt-get修复术
apt-get clean
apt-get autoclean
apt-get autoremove
apt-get update
apt-get upgrade

badblocks -v /dev/md126p1 > phoronix/md126p1_bad.log


- build-essential
- autoconf
- mesa-utils
- unzip
- apt-file

## bad interpreter
```
:set fileformat=unix
```


## 文件按大小排序
du -s /*|sort -nr
locate file

```
curl: error while loading shared libraries: libssl3.so: cannot open shared object file: No such file or directory

解决方案:
apt-get -y install curl
```
```
# 日志批量删除的办法
find . -type f -name "*.log"|xargs rm -rf *
```

# 回环设备100%
df -h
apt autoremove --purge snapd

//修改系统时间
date -s "2020-09-24 14:19:40"
date -s "14:19:40"

sed -i '$a */2 * * * * /usr/bin/php /work/web/application/crontab/Event.php' /var/spool/cron/crontabs/root