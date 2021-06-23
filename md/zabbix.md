## zabbix
###一、安装源仓库
chmod +x zabbix-release_4.0-2+bionic_all.deb
dpkg -i zabbix-release_4.0-2+bionic_all.deb
dpkg -l|grep zabbix
###二、zabbix-server-mysql和zabbix-web-mysql安装、配置
###三、数据库初始化配置;
###四、zabbox-agent安装