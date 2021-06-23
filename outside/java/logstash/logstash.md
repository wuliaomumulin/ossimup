## logstash
### 一、安装
```
tar -zxf logstash-6.7.0.tar.gz -C /usr/share/

```
### 二、测试用例
```
# 输入输出
bin/logstash -e 'input { stdin{}} output{ stdout{codec => rubydebug}}'
# 监控日志
## 测试文件是否正常使用，-t代表测试是否正常，不加正常执行
bin/logstash -f config/monitor_file.conf -t

```


#### 2、rsyslog的收集
```
tcpreplay -i eth5 -M 1 /root/andisec.pcap
tcpdump -i eth5 -nn -A port 514

bin/logstash -f config/rsyslog.conf
//后台运行
nohup bin/logstash -f config/rsyslog.conf > /dev/null 2>&1


tcpdump -i eth5 -nn -A port 515
```

