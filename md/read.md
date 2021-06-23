五元组、端口、IP、安全事件列表、威胁情报.

# mysql日志落盘
show variables like "%general_log%";
set global general_log=off
tail -n 50  /var/lib/mysql/andi.log

一、框架的问题
1、PDO事务使用貌似不管用


20200330 
编写资产增加和删除的接口
20200331
编写插件列表、以及增加和删除接口;
20200401
UDP采集器列表、以及增加和删除接口;
SNMP协议的使用和学习;
插件相关数据表新增部分字段和逻辑;
大屏前期的准备
20200402
威胁情报的列表和删除
重新修改数据库调用注释的表示方法;
20200403
系统状态接口开发、以及文档编写;
重新修改威胁情报的表结构;
针对报表上传文件的修改;

态势感知
采集器

2、这两张表
host_properties software_cpe

输出当前数组:key current


1、继承父类;
2、子类写方法;

snmp协议
```
yum list all|grep net-snmp* # 列出可以使用的包
rpm -qa|grep net-snmp* # 已安装的包
yum install --skip-broken -y net-snmp net-snmp-utils # 安装
rpm -ql net-snmp-5.7.2-38.el7_6.2.x86_64 # 查看单个安装包具体情况
```

snmpget -v协议版本 -c 指定密码 oid
snmpget -v2c -c public 192.168.1.86 .1.3.6.1.2.1.1.1.0 
oid

snmpwalk 127.0.0.1 -c public -v 2c # 抓取本机全部
snmpwalk 127.0.0.1 -c public -v 2c 1.3.6.1.2.1.1.1 # 抓取操作系统
snmpwalk 127.0.0.1 -c public -v 2c 1.3.6.1.2.1.1.3 # 抓取开机时间
snmpwalk 127.0.0.1 -c public -v 2c 1.3.6.1.2.1.1.5 # 抓取主机名称


yaf_menu
yaf_user
user_role
system_config
sys_log
user_report
udp_sensor

sql_mode = STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION

20200407
告警和安全事件
20200409
大数据平台调试问题
20200410
大屏的网卡数据抓取和展示;	
开会讨论筛选的重新调整

一、library调用与被调用
yaf-library管理
library方面
Test\Test1.php
```
class Test_Test1{
	public function test(){
		echo __FUNCTION__;
	}
}
```
controller方面
```
$Test = new Test_Test1();
$Test->test();
```
二、Ubuntu开机启动文件编辑
```
/etc/profile
```
20200413
1、确认以及排查大数据平台线上环境问题;
2、解决禅道的一堆bug;
```
set nobomb|bomb|bomb?
:set fileformat=unix
```
20200414

1、图片上传的问题

厂级平台权限apache2默认用户:www-data

2、数据发送状态;
kb|MB
3、默认大屏设置;

20200415
websocket的问题
apt-get install php7.2-gd

20200416
资产和采集器详情的接口;
相关功能的前端督促;
学习和bug
	psr4相关规范的学习;
	input的为空判断标准
	
B比特位-->KB->MB->GB


20200417
1、xml数据整理

    //修改管理口IP
    public function editmanagementipAction(){
        $str = APP_PATH.'/outside/modify_configure.py';
        `python {$str}`;
         jsonResult([],'操作成功');
    }


1、udp采集器修改;
2、采集器分类;
20200422
3、目录整合并且权限验证
4、大数据平台默认路由不生效的问题;
5、修改厂级平台表注释;
20200423
1、了解和解析xml
2、大数据平台了解导入功能异常；
20200424
3、代理转发
4、将有关es的ifarme的url地址全部换成基于服务端IP的动态获取


20200426
# 权限配置
chmod O+x /work/web/outside/cpu_mem_disk.sh
chmod -R 777 /work/web/log
# 以守护的方式运行任务
nohup php /work/web/application/bin/network.php > /dev/null 2&1 &

大屏字段调整
1、告警列表第一列新增意图和策略,对应字段category
2、安全事件第一列新增事件名称,对应字段plugin_sname
3、将告警列表和安全列表的时间字段放到最后一列;

4、登陆时把查询的theme1字段删去，以保证数据库升级版本没问题;

20200427
1、告警规则;
2、资产修改、显示字段大小写、字段错别字修正;
20200428
1、告警大屏查询慢原因排查；


20200429
# mq使用

# 原料:


# 安装mq客户端
./configure --prefix=/usr/lib/rabbitmq-c
make&&make install

# 安装扩展
./configure --with-php-config=/usr/bin/php-config --with-amqp --with-librabbitmq-dir=/usr/lib/rabbitmq-c/
make&&make install

# 配置添加
vim /etc/php/7.2/cli/conf.d/amqp.ini

[PHP Modules]
extension=amqp.so


安装rabbitmq

20200430
高级搜索、资产字段新增;

202005041244
1、添加采集器同时添加资产，编辑采集器怎么编辑资产？删除采集器怎么删除资产？
2、初期版本和后期版本不一致;



# System Information设备信息 
dmidecode -t 1
# 设备唯一标识uuid
dmidecode -s system-uuid
# serial number码
dmidecode -s system-serial-number
dmidecode -t system|grep -i 'serial number'


20200506
1、采集器和Python从厂级平台迁移的问题;
2、构图;
3、解决网段问题:
 - A、网口拔插, ip addr eth2 up|down
  python /work/agent/collect_config.py -t 19.19.19.11 -p 8801 --para 2
  python /work/sensor_manager/agent/collect_config.py -t 19.19.19.61 -p 8801 --para 2
  python /work/sensor_manager/agent/collect_config.py -t 19.19.19.52 -p 8801 --para 2

  
# 配置临时IP
ifconfig eth3:1 19.19.19.192 netmask 255.255.255.0


20200507
采集器调阅接口

# python进程管理工具
supervisorctl status
supervisorctl restart appmaster_agent

appmaster 

1、socket服务是由于缺失证书
2、依赖包pip list


20200508
1、采集器的路由、网关；
2、采集器的调阅;

一、未处理的问题
1、大数据有几个禅道的bug没有处理;
2、厂级平台采集器缺少的字段需要定制需求，和上级提.
3、采集器的字段没有;

gw_manager  qwe123!@   19.19.19.51
gw_operator  qwe123!@  19.19.19.51


python /work/sensor_manager/agent/config_sysinfo.py -t 19.19.19.51 -p 8801 --softver="vt1.1" --sysip="19.19.19.51" --devmac="000000000000" --devname="测试设备" --eventdevname="测试事件名称" --vendor=" 支持者"

软件版本: V2.0H-20200327 17:00:00 0027
MAC信息: 618666604378
装置名称:D2D
事件设备名称:Dvc
厂商名称:A1
设备地址:192.168.2.51

softver:V2.0H-20200327 17:00:00 0027
devip:192.168.2.51
devmac:618666604378
devname:D2D
eventdevname:A1
vendor:Dvc



目的地址:19.19.19.31
目的端口：514
目的地址：19.19.19.11
目的端口:5514

mysql存储过程


1、采集器资产--snmp版本 --snmptype字段修改有问题;
2、采集器资产--ostype字段修改有问题;


2、
2、升级

supervisorctl restart appmaster_agent

chmod -R 777 /var/lib/mysql
chown -R mysql:mysql /var/lib/mysql
cp -R /var/lib/mysql

mysql -e "CREATE FUNCTION fnv1a_64 RETURNS INTEGER SONAME 'libfnv1a_udf.so'"
mysql -e "CREATE FUNCTION fnv_64 RETURNS INTEGER SONAME 'libfnv_udf.so'"
mysql -e "CREATE FUNCTION murmur_hash RETURNS INTEGER SONAME 'libmurmur_udf.so'"


# shell-help
man test
-a and
-o or
-n not
-z 空
-eq 等于
-ne 等于
-ge 大于等于
-gt 大于
-le 小于等于
-lt 小于


# 字符串判断
[ "abc" = "abc" ]&&echo 1||echo 2 
# 用户输入的默认值,-z为空，-n不为空
[ -z "$RQUOTAD"] && echo 'ok'


csmd

apache 安装代理以及ssl
a2enmod rewrite
a2enmod proxy
a2enmod proxy_http

1、采集器的python其实可以通过python aaa.python > aaa.log &来解决卡死的问题，但是如果执行了太多相同次的python命令，那么这么多进程该怎么维护和管理，所以这方面肯定是有问题的。
2、公司官网维护;
3、厂级平台在国产化操作系统中的迁移配置;
4、厂级平台以及数据库在测试环境的迁移和部署;
5、phoromatic-test-suite的使用和测试;
6、厂级平台安全检测和威胁注入的防范方法和平台编码修正;
7、智慧能源的字段修改;
8、mysql的性能测试以及优化;



show status like 'innodb_row_lock%'


1、配置厂级平台服务相关;
update user set Host = '%' where Host="localhost";
flush privileges;

时区的调整


一、开发相关兼容性处理:
1、对厂级平台相关web时区做兼容性处理;
2、规范web目录结构，统一所有中文目录改成英文;
3、修正完善kibana与apache2转发的相关规则;
4、重新编写apache虚拟主机头文件去适应厂级平台V2.0的版本;
5、整理和备份针对php5.6的兼容性扩展文件,以供兼容性升级;
6、编写针对厂级平台V2.0版本针对web端的升级部署脚本;
二、运维相关:
7、使用Clonezille对操作系统做备份和恢复;
8、厂级平台对要升级的设备进行硬件基准测试;


------------------------------
一、运维相关:
1、配合硬件相关人员做多种方案raid卡的测试处理;
2、现场实施的问题的排查和处理;



一、开发相关
1、审计日志的搜索功能增加;
2、采集器管理的界面变化和接口调整;
3、智慧能源优化Elasticsearch索引模板结构,使其生成按天存储的索引;
4、重新整理shell,规范化旧版本厂级平台脚本更新文件;
5、禅道bug的处理;

二、运维测试相关
6、phoronix-test-suite集群自动化搭建测试的流程整理;
7、药总和其他厂商的硬件设备性能测试;
8、在库房，针对安全运维人员，现场讲解和答疑有关硬件性能测试的方法和思路;



一、开发任务:
1、处理禅道bug;
2、增加账户三个月密码过期的功能;
3、更改实施配置信息里面表单输入的最大值;
4、处理菜单的中文解释和错误提示;
5、智慧能源和elasticesearch索引按天分index存储;
6、公司官网的问题;(待完成)

二、运维任务:
7、硬件自动化基准测试的问题:
 I、所有硬件设备调试基本完成、操作系统已经安装完毕;(尚未对硬件进行反复部署测试的验证);
 II、已准备齐全硬件测试的基本环境，包含路由器、交换机之类的，完成组网环境;
 III、需要对硬件各项性能进行压力测试;

 






b、采集器管理的问题;
c、phoronix自动化的问题;PASS
d、公司官网的问题;
e、bug;
-------------------------------------
20200824
-----------------------
1、es的bulk插入的问题;
2、pssh的问题;


密码登录
ssh -o StrictHostKeyChecking=no 192.168.1.86

## 制作镜像方法
1、修改一下vim /etc/phoronix的URL，并且加上systemctl enable phoronix
2、再生龙拷系统为镜像

Admin username:admin
Admin password:eijayMXDxs

NV3Y2asi3e&ANDI*J

phoronix端口和账户绑定,批量性能测试的办法

阿里云邮箱
liyongbing@andisec.com admin123Aa

redis

mkdir /var/log/redis 
chown -R redis:redis /var/log/redis


部署调试中烟项目
http://192.168.66.155:4000
Administrator Administrator


## 资产类型和资产列表基于原有修改;
## 监测审计分类新增、以及相关逻辑的修改;
## 主机卫士的系统时间调整;
## 测试和部署的升级包沟通的问题
## Agent优化部分参数(如ident),采集状态性能图片;

## 验签有误 
### 采集器需配置管理平台ip

```
ll 2>&1 # 错误重定向
```


## 网络安全态势的开发;

### 外网访问类型
### 外网访问区域统计
### 外网访问资产

1、资产拓扑;
2、网络安全;
3、主机防护;(设备还是扩展)

codeigniter 1.0 
1、session出现问题，再代码中直接赋值;
2、模型命名要大小型分明;

/** 字符串截取 */
select  substr(`name`,1,4) `name` from device_types where id in(1204,1205,1206)

3、IP正则好像有问题,需要使用函数;
4、实时威胁情报的正常流程;
5、网络行为;
6、白名单网络验证处理类


1、网络安全态势大屏接口开发以及与用户权限关联;
2、封装ES数据条数查询语句,增加基于模型位置的Redis缓存的功能;
3、完成网络拓扑与用户资产结合，另外要做图标设计；
4、优化采集器查询为不管成功与否都会返回相关信息;
5、主机卫士资产状态监控;
6、优化监测审计白名单的验证规则;
7、重新优化Elasticsearch查询字段;
8、将主机防护大屏的数据查询范围缩减为一周;
9、数据库菜单、设备类型、用户权限相关表的更新;
10、工控安全事件图表和接口;

20201028
中能硬盘测试;
电厂实施和文档管理系统;
威胁情报;
网络拓扑;
主机卫士图标优化、安全防护;
1、安全防护的状态需要修改；


### mysql to many connection
```
max_connections=1000
wait_timeout=300
interactive_timeout=500
```

curl -XPOST http://127.0.0.1:9200/threatintelligence-2020*/_search?pretty -H 'Content-Type:application/json' -d '{"_source":{"includes":["id","ioc_raw","severity","created_at","find_at","update_at","family","family_desc","port","related_sample","related_ip","related_gangs","related_gangs_desc","related_events_and_desc","solution"]},"query":{"bool":{"must":[{"term":{"is_queryd":{"value":1}}},{"term":{"ti_class":{"value":0}}}]}},"from":0,"size":"50","sort":[{"@timestamp":{"order":"desc"}}]}'

*/2 * * * * /usr/bin/php /work/web/application/crontab/Event.php





# 定时任务；
# 网络拓扑；
1、聚合模式为从topology_node表重新整理线条关系;
2、离散模式为从topology_edge中整理线条关系;
3、xml解析
(1)、先去看源代码;
# 仓库测试；


1、主机卫士新增批准状态、修改主机名称;
2、平台服务搭建、编码;


运维shell与linux权限



//查询条数大于1的某个字段
select id,ip from topology_node group by ip having count(ip) > 1

1、主机卫士的在离线功能新增;
2、国资委项目
3、主机防护大屏es时间
4、主机卫士接口优化;
curl localhost:9200/_cat/indices?format=json
curl "localhost:9200/_cat/indices?v&h=index&format=json"
curl "localhost:9200/_cat/health?v&h=cluster,status&format=json" //查看主节点运行状态

```
<Directory /work/web/html/>
        Options FollowSymLinks
        AllowOverride None # 改为All可以加载加载站点内的.htaccess
        Require all granted
</Directory>
```
### thinkphp6创建定时任务
```
php think make:command Syncjson Syncjson
php think syncjson
```


#### 问题1:rm ls,Structure needs cleaning

```

df -h dump.rdb # 查看文件所在分区
debugfs -w /dev/sda5 # w表示以读写方式打开
clri /var/lib/redis/dump.rdb # 清除文件

```

#### 定时任务追加的目录
```
sed -i '$a */2 * * * * /usr/bin/php /work/web/application/crontab/Event.php' /var/spool/cron/crontabs/root
```

1、修改bug和解决需求;
2、电厂运维支持;
3、中能国资委支持;
4、风险列表和集团风险列表、以及详情增加排序和界面优化;


//预防sql注入的参数绑定
$where = [
    'port' => ':port',
    'subtype' => ':subtype',
];

$bind = [
    ':port' =>  8801,
    ':subtype' => 1101,
];
var_dump($where);
$result = $this->alias('a')->bind($bind)->where($where)->order($order)->page($page, $pagesize)->select();
echo $this->_sql();


### syslog和流量重放
```
tcpdump -i eth5 -nn -A port 514
tcpdump -i eth5 -nn -A port 514 -w /root/tes.pcap //流量输出到文件
tcpreplay -i eth5 -l 0 -M 1 /root/tes.pcap //流量重放
```

- ossim-web账户和密码 admin is 4ruqfmXK

- 安装ossim系统
- Vulnerabilities 脆弱性 检测 No
- Hids 基于主机的入侵检测 Not Deployed Connected


1、资产组、网络组参考ossim;
2、厂级平台分区、权限
3、kibana管道、汉化研究;
4、官网迁移;
5、logstash转发;
6、工业漏洞库的整理;
7、学习java;



1、北京京能未来燃气热电;
2、三峡集团流量转发的问题;(未部署)
3、广西大唐平班水电;
4、中烟项目性能调试;(未定位)
5、定时服务拉起脚本编码;
6、有奇卫士授权CPU、磁盘高阈值问题;(授权问题以定位，CPU问题正在排查原因)
7、北京京能高安屯燃气热电;(弹出问题)

20210119


1、捷峰风电厂;
 - 非法关机导致,系统损坏

2、青铜峡铝业发电有限公司;
 - 设备灯不亮，网络故障，问题很大;

3、Local Suites
4、
- 软件:
 - 业务分区和系统分区、日志分区、数据分区分离;
 - 缺少有效的备份还原手段;

curl -XDELETE localhost:9200/graylog_0

大唐石城子光伏电站

--- 
phoromatic.connect
phoronix-test-suite phoromatic.connect

//调试
pts_client::$pts_logger && pts_client::$pts_logger->log($file);

连接到互联网其官网的配置中心进行配置;
遂修改客户端
echo "192.168.1.2:5555/I0SSJY" > /var/lib/phoronix-test-suite/modules-data/phoromatic/last-phoromatic-server

