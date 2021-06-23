# 建立phoronix.service文件
sudo vim /etc/systemd/system/phoronix.service

将下列内容复制进phoronix.service文件
[Unit]
Description=/etc/phoronix Compatibility
ConditionPathExists=/etc/phoronix
 
[Service]
Type=forking
ExecStart=/etc/phoronix start
TimeoutSec=0
StandardOutput=tty
RemainAfterExit=yes
SysVStartPriority=99
 
[Install]
WantedBy=multi-user.target


创建文件phoronix
sudo vim /etc/phoronix

将下列内容复制进phoronix文件


#!/bin/sh -e
#
# phoronix
#
# This script is executed at the end of each multiuser runlevel.
# Make sure that the script will "exit 0" on success or any other
# value on error.
#
# In order to enable or disable this script just change the execution
# bits.
#
# By default this script does nothing.
nohup phoronix-test-suite phoromatic.connect 19.19.19.11:8893/KR0XVG > /var/log/phoronix/phoronix.log &
exit 0

给phoronix加上权限,启用服务

sudo chmod +x /etc/phoronix
sudo systemctl enable phoronix
启动服务并检查状态

sudo systemctl start phoronix.service
sudo systemctl status phoronix.service
重启并检查test.log文件
cat /usr/local/test.log

