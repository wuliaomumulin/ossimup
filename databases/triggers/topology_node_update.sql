use alienvault;

-- 同步告警数量

drop trigger if exists topology_node_update;
delimiter //
 create trigger topology_node_update after update on alienvault.topology_node for each row begin
update alienvault.host a inner join alienvault.host_ip b on a.id=b.host_id set a.alert = new.alert where inet6_ntoa(b.ip)=new.ip;
 end//
delimiter ;
