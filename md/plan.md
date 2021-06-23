## Mysql计划任务
> 就像window有计划任务，linux有crontab，mysql其实也有计划任务.

### 临时开启 
```
show EVENTS;
show VARIABLES like "event_scheduler";
set GLOBAL event_scheduler = On;
/*会显示一个event_scheduler的用户拥有Daemon线程;*/
show processlist;
```
### 永久开启
#### 在my.cnf或者mysqld.cnf中加入以下内容，重启server
```
event_scheduler = 1
```

#### 简单的测试
```
-- 创建一个表
create table aaa(ctime timestamp);
每三秒插入一条数据
create event e_test_insert on schedule every 3 second do insert into test.aaa values(current_timestamp);

-- 五天后定时清空aaa表
create event e_del5_test on schedule at current_timestamp + interval 5 day do truncate table test.aaa

-- 指定时间清空某表
create event e_20201022100000_test on schedule at timestamp '2020-10-22 10:00:00' do truncate table test.aaa;

-- 每天当时清空某表
create event e_20201022120000_test on schedule every 1 day comment '每天当时清空某表' do truncate table test.aaa;
create event e_5after_del_test on schedule every 1 day starts current_timestamp + interval 5 day comment '五天后开启每天定时清空某表' do truncate table test.aaa
create event e_5after_end_aaa on schedule every 1 day ends current_timestamp + interval 5 day comment '每天定时清空某表直到五天后' do truncate table test.aaa
create event e_1m_after_end_aaa on schedule every 1 day starts current_timestamp + interval 5 day ends current_timestamp + interval 1 month comment '五天后每天定时清空某表直到一个月后' do truncate table test.aaa
```

### 简单的物化视图解决方案
#### (1)、不使用存储过程
> 在某些场景下，例如大屏或横屏开发时，当数据表内容太大时，可以做一个临时表，然后每隔一段时间查询原表数据放到零时表中，业务直接查询临时表就行了。

##### 新建一个event.sql，delimiter代表每句的分隔符为指定字符直到遇到end,使用它和mysql内置的分隔符;有关，不然后面的语句就会由于;造成语法错误,当然也可以使用call方法的方式来逃避它,

```
use test;
drop event if exists e_1m_after_end_aaa;
create event e_1m_after_end_aaa on schedule every 1 day starts current_timestamp + interval 5 day ends current_timestamp + interval 1 month comment '五天后每天定时清空某表直到一个月后' do truncate table test.aaa;

drop event if exists copy_aaa_100_aaascreen;
delimiter //
CREATE EVENT `copy_aaa_100_aaascreen` ON SCHEDULE EVERY 5 MINUTE STARTS current_timestamp COMMENT '每五分钟做一次数据同步' DO begin
truncate table test.aaa_screen;
insert into test.aaa_screen select * from test.aaa order by ctime desc limit 0,100;
end//
delimiter ;
```

##### vi的清空文本方式为命令行执行:,$d
#### (2)、使用存储过程

```
use test;

drop event if exists e_1m_after_end_aaa;
create event e_1m_after_end_aaa on schedule every 1 day starts current_timestamp + interval 5 day ends current_timestamp + interval 1 month comment '五天后每天定时清空某表直到一个月后' do truncate table test.aaa;

drop procedure if exists copy_aaa_100_aaascreen;
delimiter //
create procedure copy_aaa_100_aaascreen()
begin
truncate table test.aaa_screen;
insert into test.aaa_screen select * from test.aaa order by ctime desc limit 0,100;
end//
delimiter ;
drop event if exists copy_aaa_100_aaascreen;
CREATE EVENT `copy_aaa_100_aaascreen` ON SCHEDULE EVERY 5 MINUTE STARTS current_timestamp COMMENT '每五分钟做一次数据同步' DO call copy_aaa_100_aaascreen();
```