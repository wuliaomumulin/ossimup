//解压
unzip hyperf.zip -d hyperf
// 误解压撤销
zipinfo -1 hyperf.zip |xargs rm -rf

//docker文件拷贝 宿主机目录 容器ID:容器目录
docker cp /root/factory 2324eac7b19b:/home/

//docker容器信息-IP地址
docker inspect 2324{容器}|grep IPAddress

# 运行一个容器后进入容器,-p 宿主机端口:容器端口 -v 宿主机目录:容器映射目录 进入容器的命令
docker run -itd -p 83:80 -v /work/web:/var/www test /bin/bash
# 进入一个容器
docker exec -it 8f7c /bin/bash

docker中socat映射宿主机器的方法:
socat TCP4-LISTEN:3306,reuseaddr,fork TCP4:17.17.17.17:3306 >/dev/null 2>&1 &
socat TCP4-LISTEN:6379,reuseaddr,fork TCP4:17.17.17.17:6379 >/dev/null 2>&1 &