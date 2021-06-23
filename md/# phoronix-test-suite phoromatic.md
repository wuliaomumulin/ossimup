## (一、phoronix-test-suite)、使用phoromatic本机测试磁盘性能
> phoronix-test-suite是一个基准测试工具，可用它来做压力测试，不巧的是需要连到OpenBenchMarking.org下载我们使用的套件，而我们环境又没有internet,所以我们可以预先下载我们需要的套件到本地，然后再导入到我们的测试环境即可使用。
### 一、安装
#### 1、deb包安装
```
//源地址
wget http://phoronix-test-suite.com/releases/phoronix-test-suite-9.6.1.tar.gz 
wget http://phoronix-test-suite.com/releases/repo/pts.debian/files/phoronix-test-suite_9.6.1_all.deb

scp root@19.19.19.11:/root/zabbix/phoronix-test-suite_9.6.1_all.deb ./
dpkg -i phoronix-test-suite_9.6.1_all.deb
```
#### 2、套件安装
##### 1、将套件目录copy到安装目录
```
tar -zxf pts.tar.gz -C /var/lib/phoronix-test-suite/installed-tests/
```
##### 2、apt源编辑
- 虽然安装了插件，但是还有可能没有安装这些套件依赖的扩展包，导致无法使用，所以我们需要通过安装工具apt去安装扩展。
- 编辑vim /etc/apt/sources.list文件，加入以下源地址:
```
deb http://192.168.1.7:8081/repository/ubuntu-aliyun/ bionic-security main restricted
deb http://192.168.1.7:8081/repository/ubuntu-aliyun/ bionic-security universe
deb http://192.168.1.7:8081/repository/ubuntu-aliyun/ bionic-security multiverse
```
##### 2、apt-get修复
```
apt-get clean
apt-get autoclean
apt-get autoremove
apt-get update
apt-get upgrade
```
##### 3、安装需要的扩展
```
apt-get install build-essential
apt-get install autoconf
apt-get install mesa-utils
apt-get install unzip
apt-get install apt-file
```

### 二、测试使用
```
// 1、pts/tiobench是一个在linux下测试多线程的软件工具，对测试多线程的读写非常有用，可以指定文件块的大小，我们通过运行以下命令来指定一个测试套件.
phoronix-test-suite benchmark pts/tiobench
// 2、pts/smallpt是一个CPU测试套件
phoronix-test-suite benchmark pts/smallpt
//3、一个内存测试套件
phoronix-test-suite benchmark pts/ramspeed
```

phoromatic 佛满忒克

