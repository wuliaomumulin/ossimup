## 端口扫描
```
pkt = IP()/TCP() # 创建一个带有IP和TCP协议的包体
pkt = IP(src="19.19.19.11",dst="19.19.19.72")/TCP()
pkt.show() # 查看包体的内容
res = sr1(pkt) # 收一个包
res = srp1(pkt) # 发送两层数据包，收一个包
res = sr(pkt) # 收全部包
res = srp(pkt) # 发送两层数据包，收全部包
send(pkt) # 只发包，不接受
res.summary() # 返回包的简述
res.show # 单行的返回包的详述
res.show() # 美观的返回包的详述
hexdump() # 16进制转换
sniff() # 网络嗅探
rdpcap() # 读取网络包
```
- 命令行
```
lsc() # 列出支持的方法
ls(TCP) # 列出支持的协议,并且支持的参数
```
## 模拟三次握手
```
#!/usr/bin/env python
# coding=utf-8

from scapy.all import *

if __name__ == '__main__':
	source=IP(dst = "19.19.19.72",src = "19.19.19.11")/TCP(dport=22)
	rsp=sr1(source)
	source2=IP(dst="19.19.19.72",src="19.19.19.11")/TCP(dport=22,flags="A",seq=rsp.ack,ack=rsp.seq+1)
	rsp2 = sr1(source2)
	print(rsp2.show())
```

## 端口扫描
```
#!/usr/bin/env python
# coding=utf-8
from scapy.all import *

conf.verb = 0
ip = input("")

```


## 一、抓包示例

```
#!/usr/bin/env python

# coding=utf-8



from scapy.all import *

dpkt = sniff(iface = 'eth5', count = 100, filter = "ip dst 19.19.19.11 or ip src 19.19.19.11")
wrpcap("liyb.pcap",dpkt)

dpkt[3][IP].src

```
## 二、包体查看与修改
    	# pks.subtype = 6
    	# print(pks.show())

包体格式
```
<CMA_Header  type=Manager subtype=17 cmdpara=Query |>>
```

资产扫描




# 构造协议
p=Ether()/IP(dst="19.19.19.11")/TCP()/"GET /api.php HTTP/1.0"
p.show
hexdump(p)
p.sprintf('Etp.sprintf('Ethnerent source:%Ether.src%,IP src:%IP.src%,%Raw.load%')