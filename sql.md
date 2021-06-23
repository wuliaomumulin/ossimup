## 慢查询

### 一

SELECT
    hex( c.backlog_id ) backlog_id,
    hex( c.event_id ) event_id,
    hex( c.corr_engine_ctx ) corr_engine_ctx,
    c.timestamp,
    c.`status`,
    c.protocol,
    INET6_NTOA ( c.src_ip ) src_ip,
    INET6_NTOA ( c.dst_ip ) dst_ip,
    c.src_port,
    c.dst_port,
    c.risk,
    c.plugin_id,
    c.plugin_sid,
    ki.NAME AS kingdom,
    ca.NAME AS category,
    ta.subcategory 
FROM (
SELECT
    backlog_id,
    event_id,
    corr_engine_ctx,
    timestamp,
    `status`,
    protocol,
    src_ip,
    dst_ip,
    src_port,
    dst_port,
    risk,
    plugin_id,
    plugin_sid 
FROM
    alarm a 
WHERE
    a.`status` = 'open' 
    AND ( a.timestamp <> '1970-01-01 00:00:00' ) 
    AND ( a.is_read = 0 ) 
GROUP BY
    a.similar 
ORDER BY
    a.timestamp DESC 
    LIMIT 0,
    50
    ) c
    LEFT JOIN (
    alarm_taxonomy ta
    LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id
    LEFT JOIN alarm_categories ca ON ta.category = ca.id 
    ) ON c.plugin_sid = ta.sid 
    AND c.corr_engine_ctx = ta.engine_id


### 二

SELECT
    hex(a.id) id,
    a.hostname,
    inet6_ntoa (b.ip) ip,
    hex(b.mac) mac,
    a.asset,
    a.fqdns,
    a.alert,
    a.created,
    a.updated,
    d. NAME,
    f.service ports
FROM
    HOST a
LEFT JOIN host_ip b ON a.id = b.host_id
LEFT JOIN host_sensor_reference c ON a.id = c.host_id
LEFT JOIN udp_sensor d ON unhex(d.host_id) = c.sensor_id
LEFT JOIN host_types e ON a.id = e.host_id
LEFT JOIN host_services f ON f.host_id = a.id
ORDER BY
    a.updated DESC
LIMIT 0,
 50


### 三、
SELECT hex(a.id) id,a.device_id,hex(a.ctx) agent_ctx,a.timestamp,a.plugin_id,a.plugin_sid,a.ip_proto as protocol,INET6_NTOA(a.ip_src) src_ip,INET6_NTOA(a.ip_dst) dst_ip,a.layer4_sport as src_port,a.layer4_dport as dst_port,a.ossim_risk_c as risk,a.src_hostname,a.dst_hostname,hex(a.src_mac) src_mac,hex(a.dst_mac) dst_mac,hex(a.src_host) src_host,hex(a.dst_host) dst_host,INET6_NTOA(a.src_net) src_net,INET6_NTOA(a.dst_net) dst_net FROM alienvault_siem.acid_event a where a.device_id=19 ORDER BY a.TIMESTAMP DESC LIMIT 0,1000


## 优化之后的sql

吴长城
### 一

SELECT
    COUNT( 1 ) AS tp_count 
FROM
    alarm a
    LEFT JOIN (
    alarm_taxonomy ta


    ) ON a.plugin_sid = ta.sid 
    AND a.corr_engine_ctx = ta.engine_id
WHERE
    a.STATUS = 'open' 
    AND ( a.src_ip = INET6_ATON ( '10.157.10.185' ) ) 
    AND ( ta.kingdom = 1 ) 
    AND ( ta.category = 1 )

### 二
SELECT hex(a.backlog_id) backlog_id,hex(a.event_id) event_id,hex(a.corr_engine_ctx) corr_engine_ctx,a.timestamp,a.status,a.protocol,INET6_NTOA(a.src_ip) src_ip,INET6_NTOA(a.dst_ip) dst_ip,a.src_port,a.dst_port,a.risk,a.plugin_id,a.plugin_sid,ki.NAME AS kingdom,ca.NAME AS category,ta.subcategory FROM alarm a  LEFT JOIN (alarm_taxonomy ta
                 LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id
                LEFT JOIN alarm_categories ca ON ta.category = ca.id 
                ) ON a.plugin_sid = ta.sid 
                AND a.corr_engine_ctx = ta.engine_id,
                backlog b   WHERE a.STATUS = 'open' AND a.backlog_id = b.id AND (  b.TIMESTAMP <> '1970-01-01 00:00:00' ) ORDER BY a.TIMESTAMP DESC LIMIT 0,50
