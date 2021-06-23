#!/bin/bash

# liyb
# 关闭平台数据库IP访问限制


mysql -uroot -p123456 -e "update alienvault.config set value = 0 where conf = 'enable_request_ip'"
