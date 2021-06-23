#!/bin/bash

# liyb

# scp -r root@192.168.1.86:/root/phoronix ./
# 免密码传输 scp .ssh/id_rsa.pub root@192.168.1.86:/root/.ssh/authorized_keys

# 测试用例的安装
workdir=$(cd $(dirname $0); pwd)
dpkg -i $workdir'/'phoronix-test-suite_9.6.1_all.deb
cp $workdir'/'sources.list /etc/apt/sources.list
mkdir -p /var/lib/phoronix-test-suite/installed-tests
tar -zxf $workdir'/'pts.tar.gz -C /var/lib/phoronix-test-suite/installed-tests/

# grub
cp $workdir'/'grub /etc/default/grub
update-grub

apt-get update
# apt-get下载的包目录 /var/cache/apt/archives/
apt-get install php
apt-get --fix-broken -y install
apt-get -y install build-essential autoconf mesa-utils unzip apt-file

# 加入开机启动服务
cp -rf $workdir'/joinservice/'phoronix /etc/
cp -rf $workdir'/joinservice/'phoronix.service /etc/systemd/system/
# 修改基础配置
cp -rf $workdir'/joinservice/'phoronix-test-suite.xml /etc/
# log放置
mkdir -p /var/log/phoronix
chmod +x /etc/phoronix

# systemctl enable phoronix
# sudo systemctl start phoronix.service
# sudo systemctl status phoronix.service

# 关闭外网访问
cp -rf $workdir'/joinservice/'pts_network.php /usr/share/phoronix-test-suite/pts-core/objects/
