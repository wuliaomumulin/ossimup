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
apt-get install php
apt-get --fix-broken install
