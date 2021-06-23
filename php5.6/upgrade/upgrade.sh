#!/bin/bash

# liyb
# ***更新php扩展相关******
init_php(){
	rm -rf /usr/lib/php5/20131226-backup
	mv /usr/lib/php5/20131226 /usr/lib/php5/20131226-backup
	cp -rf /work/install/web/package/php/lib/20131226 /usr/lib/php5/20131226
	rm -rf /etc/php5-backup
	mv /etc/php5 /etc/php5-backup
	cp -rf /work/install/web/package/php/php5 /etc/
}



# python
# 采集器相关
init_python(){

	chmod -R 777 /work/sensor_manager/agent/return/monitor

}
# apache相关

init_apache2(){
	rm -f /etc/apache2/sites-enabled/alienvault-ssl.conf 
	rm -f /etc/apache2/sites-enabled/ossim-framework.conf
	cp /work/install/web/package/apache2/andisec.conf /etc/apache2/sites-enabled/
	cp /work/install/web/package/apache2/andisec-ssl.conf /etc/apache2/sites-enabled/

	/etc/init.d/apache2 restart
}

# Elasticsearch相关
init_elasticsearch(){
	curl -XPUT localhost:9200/_template/zhny_smart_collect -H 'content-Type:application/json' -d '{"order":2147483647,"version":2,"index_patterns":["zhny_smart_collect*"],"mappings":{"dynamic":false,"properties":{"vendor":{"type":"text"},"dev":{"type":"integer"},"ts":{"type":"date","format":"yyyy-MM-dd HH:mm:ss"},"tag":{"type":"keyword"},"value":{"type":"integer"},"device":{"type":"text"},"dst_ip":{"type":"text"},"src_ip":{"type":"text"},"type":{"type":"text"},"ver":{"type":"text"}}}}'
	curl -XPUT localhost:9200/_template/zhny_enterprise_report -H 'content-Type:application/json' -d '{"order":2147483647,"version":2,"index_patterns":["zhny_enterprise_report*"],"mappings":{"dynamic":false,"properties":{"vendor":{"type":"text"},"dev":{"type":"integer"},"ts":{"type":"date","format":"yyyy-MM-dd HH:mm:ss"},"tag":{"type":"keyword"},"value":{"type":"integer"}}}}'

}



init_php
init_python
init_apache2
init_elasticsearch

echo '--------------------------------------------------'
echo "ok"
