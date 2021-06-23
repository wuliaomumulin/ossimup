# Rsyslog使用
##一、rules规则
```
*.info;mial.none;authpriv.none;cron.none   /var/log/messages
# 所有设备的所有info以及info以上的日志都记录的这个日志当中，并且排除mail、authpriv、cron.
authpriv.* /var/log/secure
mail.*      /var/log/mail
cron.*      /var/log/cron
*.emerg     :omusrmsg:*
# 任何设备的严重级别的都会打印到屏幕终端

```
### 二、level级别
```
man 3 syslog
# 查询syslog说明
```

### 三、发送一条日志
```
logger -p cron.info "run.liyb"

logger -p emerg "hello,liyb..."
# 灾难级别
tail -f /var/log/cron.log
```


# lsof |grep sshd_config
# kill -l

# 正则图片替换
<html>
<head></head>
<body>
	<img src="http://www.baidu.com/aaa.img" alt="aaa.img" width="100" height="111" />
	<img src="http://www.baidu.com/bbb.img" alt="aaa.img" width="100" height="111" />
</body>
</html>
# <img\s*src=['|\"](.*?)\".* //图片url匹配
# $1