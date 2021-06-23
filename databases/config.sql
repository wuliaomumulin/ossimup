/*
 Navicat Premium Data Transfer

 Source Server         : 19.19.19.70
 Source Server Type    : MySQL
 Source Server Version : 50647
 Source Host           : 19.19.19.70:3306
 Source Schema         : alienvault

 Target Server Type    : MySQL
 Target Server Version : 50647
 File Encoding         : 65001

 Date: 14/12/2020 03:19:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`  (
  `conf` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` text CHARACTER SET latin1 COLLATE latin1_general_ci NULL,
  PRIMARY KEY (`conf`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `config` VALUES ('acid_link', '/ossim/forensics/');
INSERT INTO `config` VALUES ('acid_path', '/work/web/ossim/www/forensics/');
INSERT INTO `config` VALUES ('adodb_path', '/usr/share/adodb/');
INSERT INTO `config` VALUES ('agg_function', '1');
INSERT INTO `config` VALUES ('alarms_expire', 'no');
INSERT INTO `config` VALUES ('alarms_generate_incidents', 'no');
INSERT INTO `config` VALUES ('alarms_lifetime', '16');
INSERT INTO `config` VALUES ('alarm_chart', '/app/kibana#/dashboard/f3ad74e0-34dd-11eb-b22e-919ef18b779b?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-1d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('alarm_list_config', '{\"key\":\"timestamp\",\"description\":\"\\u65f6\\u95f4\"},{\"key\":\"src_ip\",\"description\":\"\\u6e90IP\"},{\"key\":\"src_port\",\"description\":\"\\u6e90\\u7aef\\u53e3\"},{\"key\":\"dst_ip\",\"description\":\"\\u76ee\\u7684IP\"},{\"key\":\"dst_port\",\"description\":\"\\u76ee\\u7684\\u7aef\\u53e3\"},{\"key\":\"category\",\"description\":\"\\u544a\\u8b66\\u7c7b\\u578b\"},{\"key\":\"kingdom\",\"description\":\"dom\\u5f62\\u5f0f\"},{\"key\":\"risk\",\"description\":\"\\u91cd\\u8981\\u7a0b\\u5ea6\"},{\"key\":\"sensor\",\"description\":\"\\u6240\\u5728\\u533a\"},{\"key\":\"plugin_id\",\"description\":\"\\u63d2\\u4ef6id\"},{\"key\":\"plugin_sid\",\"description\":\"\\u5b50\\u63d2\\u4ef6id\"},{\"key\":\"status\",\"description\":\"\\u72b6\\u6001\"},{\"key\":\"protocol\",\"description\":\"\\u534f\\u8bae\"}');
INSERT INTO `config` VALUES ('arpwatch_path', '/usr/sbin/arpwatch');
INSERT INTO `config` VALUES ('asset_chart', '/app/kibana#/dashboard/967c5700-34dd-11eb-b22e-919ef18b779b?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-1d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('asset_open_ports', '23,135,137,138,139,161,445,3389');
INSERT INTO `config` VALUES ('audit_db_set', '1');
INSERT INTO `config` VALUES ('audit_db_threshold', '100000');
INSERT INTO `config` VALUES ('audit_space_threshold', '20');
INSERT INTO `config` VALUES ('backup_base', 'alienvault_siem');
INSERT INTO `config` VALUES ('backup_conf_pass', '');
INSERT INTO `config` VALUES ('backup_day', '2');
INSERT INTO `config` VALUES ('backup_dir', '/var/lib/ossim/backup');
INSERT INTO `config` VALUES ('backup_encryption', '');
INSERT INTO `config` VALUES ('backup_events', '100000');
INSERT INTO `config` VALUES ('backup_events_min_free_disk_space', '15');
INSERT INTO `config` VALUES ('backup_host', '127.0.0.1');
INSERT INTO `config` VALUES ('backup_hour', '23:59:59');
INSERT INTO `config` VALUES ('backup_netflow', '15');
INSERT INTO `config` VALUES ('backup_pass', '\\zae]òKF’oóŸ‰—¸e');
INSERT INTO `config` VALUES ('backup_port', '3306');
INSERT INTO `config` VALUES ('backup_store', '0');
INSERT INTO `config` VALUES ('backup_timepicker', '02:00');
INSERT INTO `config` VALUES ('backup_type', 'mysql');
INSERT INTO `config` VALUES ('backup_user', 'root');
INSERT INTO `config` VALUES ('bi_pass', '\\zae]òKF’oóŸ‰—¸e');
INSERT INTO `config` VALUES ('can_request_ip', '19.19.19.19/255.255.255.0-19.19.19.128/255.255.255.0-19.19.19.143/255.255.255.0');
INSERT INTO `config` VALUES ('copy_siem_events', 'no');
INSERT INTO `config` VALUES ('customize_send_logs', NULL);
INSERT INTO `config` VALUES ('customize_subtitle_background_color', '#7A7A7A');
INSERT INTO `config` VALUES ('customize_subtitle_foreground_color', '#FFFFFF');
INSERT INTO `config` VALUES ('customize_title_background_color', '#8CC221');
INSERT INTO `config` VALUES ('customize_title_foreground_color', '#000000');
INSERT INTO `config` VALUES ('customize_wizard', '0');
INSERT INTO `config` VALUES ('custom_host_ip', '119.119.119.119');
INSERT INTO `config` VALUES ('custorm_host_ip', '0.0.0.0');
INSERT INTO `config` VALUES ('day', '5');
INSERT INTO `config` VALUES ('dc_acc', '');
INSERT INTO `config` VALUES ('dc_ip', '');
INSERT INTO `config` VALUES ('dc_pass', '');
INSERT INTO `config` VALUES ('default_context_id', '16371233-c07a-11e8-a872-493e58020307');
INSERT INTO `config` VALUES ('default_engine_id', '163715f8-c07a-11e8-a872-493e58020307');
INSERT INTO `config` VALUES ('def_asset', '2');
INSERT INTO `config` VALUES ('disk_space', '15');
INSERT INTO `config` VALUES ('email_body_template', '');
INSERT INTO `config` VALUES ('email_subject_template', '');
INSERT INTO `config` VALUES ('enable_idm', '1');
INSERT INTO `config` VALUES ('enable_request_ip', '0');
INSERT INTO `config` VALUES ('encryption_key', 'AEA94D56-7CA8-BFC9-E7AF-AD9EDAA0844A');
INSERT INTO `config` VALUES ('events', '2000000');
INSERT INTO `config` VALUES ('event_chart', '/app/kibana#/dashboard/0ef3c2c0-dd5b-11ea-9c0b-9f7aceacfcaa?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-1d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('event_list_config', '{\"key\":\"timestamp\",\"description\":\"\\u65f6\\u95f4\"},{\"key\":\"src_ip\",\"description\":\"\\u6e90IP\"},{\"key\":\"src_port\",\"description\":\"\\u6e90\\u7aef\\u53e3\"},{\"key\":\"dst_ip\",\"description\":\"\\u76ee\\u7684IP\"},{\"key\":\"dst_port\",\"description\":\"\\u76ee\\u7684\\u7aef\\u53e3\"},{\"key\":\"eventname\",\"description\":\"\\u5b89\\u5168\\u4e8b\\u4ef6\\u540d\\u79f0\"},{\"key\":\"risk\",\"description\":\"\\u91cd\\u8981\\u7a0b\\u5ea6\"},{\"key\":\"protocol\",\"description\":\"\\u901a\\u4fe1\\u534f\\u8bae\"},{\"key\":\"dst_hostname\",\"description\":\"\\u6240\\u5c5e\\u8d44\\u4ea7\"},{\"key\":\"plugin_id\",\"description\":\"\\u63d2\\u4ef6id\"},{\"key\":\"plugin_sid\",\"description\":\"\\u5b50\\u63d2\\u4ef6id\"},{\"key\":\"interface\",\"description\":\"\\u7f51\\u53e3\"},{\"key\":\"src_mac\",\"description\":\"\\u6e90MAC\\u5730\\u5740\"},{\"key\":\"dst_mac\",\"description\":\"\\u76ee\\u6807MAC\\u5730\\u5740\"},{\"key\":\"src_host\",\"description\":\"\\u6e90\\u670d\\u52a1\\u5668\\u5730\\u5740\"},{\"key\":\"dst_host\",\"description\":\"\\u76ee\\u6807\\u670d\\u52a1\\u5668\\u5730\\u5740\"},{\"key\":\"src_net\",\"description\":\"\\u6e90\\u7f51\\u7edc\\u5730\\u5740\"},{\"key\":\"dst_net\",\"description\":\"\\u76ee\\u6807\\u5730\\u7f51\\u7edc\\u5740\"}');
INSERT INTO `config` VALUES ('event_stat', '/app/kibana#/dashboard/4335bd70-d7d6-11ea-af1a-598c611dc23d?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-1d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('event_viewer', 'base');
INSERT INTO `config` VALUES ('expire', 'yes');
INSERT INTO `config` VALUES ('failed_retries', '5');
INSERT INTO `config` VALUES ('fail_count', '5');
INSERT INTO `config` VALUES ('fail_time', '15');
INSERT INTO `config` VALUES ('first_login', 'no');
INSERT INTO `config` VALUES ('flow_active_chart', '/app/kibana#/dashboard/f0ad1350-d7ad-11ea-97d0-3d8ea5331f88?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3A\'2020-08-06T01%3A21%3A54.911Z\'%2Cto%3Anow))');
INSERT INTO `config` VALUES ('flow_event_chart', '/app/kibana#/dashboard/711e23f0-d78e-11ea-97d0-3d8ea5331f88?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3A\'2020-08-06T01%3A21%3A54.911Z\'%2Cto%3Anow))');
INSERT INTO `config` VALUES ('flow_status_chart', '/app/kibana#/dashboard/4ee8e240-d2ff-11ea-8e1c-75766aa7d112?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!f%2Cvalue%3A900000)%2Ctime%3A(from%3Anow-7d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('font_path', '/usr/share/fonts/truetype/ttf-bitstream-vera/Vera.ttf');
INSERT INTO `config` VALUES ('fpdf_path', '/usr/share/fpdf/');
INSERT INTO `config` VALUES ('frameworkd_address', '127.0.0.1');
INSERT INTO `config` VALUES ('frameworkd_backup_days_lifetime', '30');
INSERT INTO `config` VALUES ('frameworkd_backup_dir', '/work/etc/ossim/framework/backups/');
INSERT INTO `config` VALUES ('frameworkd_backup_period', '300');
INSERT INTO `config` VALUES ('frameworkd_backup_storage_days_lifetime', '100');
INSERT INTO `config` VALUES ('frameworkd_businessprocesses_period', '300');
INSERT INTO `config` VALUES ('frameworkd_dir', '/work/framework');
INSERT INTO `config` VALUES ('frameworkd_donagios', '1');
INSERT INTO `config` VALUES ('frameworkd_eth', 'eth5');
INSERT INTO `config` VALUES ('frameworkd_gateway', '127.0.0.1');
INSERT INTO `config` VALUES ('frameworkd_keyfile', '/work/etc/ossim/framework/db_encryption_key');
INSERT INTO `config` VALUES ('frameworkd_listener', '1');
INSERT INTO `config` VALUES ('frameworkd_log_dir', '/var/log/ossim/');
INSERT INTO `config` VALUES ('frameworkd_mask', '255.255.255.0');
INSERT INTO `config` VALUES ('frameworkd_nagiosmklivemanager', '1');
INSERT INTO `config` VALUES ('frameworkd_nagios_mkl_period', '300');
INSERT INTO `config` VALUES ('frameworkd_nagios_sock_path', '/var/lib/nagios3/rw/live');
INSERT INTO `config` VALUES ('frameworkd_nfsen_config_dir', '/etc/nfsen/nfsen.conf');
INSERT INTO `config` VALUES ('frameworkd_nfsen_monit_config_dir', '/etc/monit/alienvault/nfcapd.monitrc');
INSERT INTO `config` VALUES ('frameworkd_notificationfile', '/var/log/ossim/framework-notifications.log');
INSERT INTO `config` VALUES ('frameworkd_port', '40003');
INSERT INTO `config` VALUES ('frameworkd_rdd_period', '300');
INSERT INTO `config` VALUES ('frameworkd_rrd_bin', '/usr/bin/rrdtool');
INSERT INTO `config` VALUES ('frameworkd_scheduled_period', '300');
INSERT INTO `config` VALUES ('frameworkd_scheduler', '1');
INSERT INTO `config` VALUES ('frameworkd_usehttps', '0');
INSERT INTO `config` VALUES ('framework_http_ca_cert_plain', NULL);
INSERT INTO `config` VALUES ('framework_http_cert_plain', NULL);
INSERT INTO `config` VALUES ('framework_http_pem_plain', NULL);
INSERT INTO `config` VALUES ('from', 'no-reply@localhost.localdomain');
INSERT INTO `config` VALUES ('glpi_link', '');
INSERT INTO `config` VALUES ('graph_link', '../report/graphs/draw_rrd.php');
INSERT INTO `config` VALUES ('have_scanmap3d', '0');
INSERT INTO `config` VALUES ('host', '1.1.1.1');
INSERT INTO `config` VALUES ('host_event_chart', '/app/kibana/app/kibana#/dashboard/e481e190-1138-11eb-a9e8-cde36546f806?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-15d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('idm_user_login_timeout', '24');
INSERT INTO `config` VALUES ('incidents_incharge_default', 'admin');
INSERT INTO `config` VALUES ('inspection_window', '0');
INSERT INTO `config` VALUES ('interface_port', 'eth3');
INSERT INTO `config` VALUES ('internet_connection', '1');
INSERT INTO `config` VALUES ('jpgraph_path', '/work/web/ossim/www/graphs/jpgraph/');
INSERT INTO `config` VALUES ('language', 'en_GB');
INSERT INTO `config` VALUES ('last_update', '2018-06-20');
INSERT INTO `config` VALUES ('latest_asset_change', '2020-03-13 03:45:36');
INSERT INTO `config` VALUES ('lifetime', '5');
INSERT INTO `config` VALUES ('locale_dir', '/usr/share/locale');
INSERT INTO `config` VALUES ('logger_expire', NULL);
INSERT INTO `config` VALUES ('logger_storage_days_lifetime', '0');
INSERT INTO `config` VALUES ('login_enable_ldap', 'no');
INSERT INTO `config` VALUES ('login_enforce_existing_user', 'yes');
INSERT INTO `config` VALUES ('login_ldap_baseDN', '');
INSERT INTO `config` VALUES ('login_ldap_bindDN', '');
INSERT INTO `config` VALUES ('login_ldap_cn', 'cn');
INSERT INTO `config` VALUES ('login_ldap_filter_to_search', '');
INSERT INTO `config` VALUES ('login_ldap_o', 'o=company');
INSERT INTO `config` VALUES ('login_ldap_ou', 'ou=people');
INSERT INTO `config` VALUES ('login_ldap_port', '');
INSERT INTO `config` VALUES ('login_ldap_require_a_valid_ossim_user', '');
INSERT INTO `config` VALUES ('login_ldap_server', '127.0.0.1');
INSERT INTO `config` VALUES ('login_ldap_ssl', '');
INSERT INTO `config` VALUES ('login_ldap_tls', '');
INSERT INTO `config` VALUES ('login_ldap_valid_pass', '');
INSERT INTO `config` VALUES ('login_session_time', '5');
INSERT INTO `config` VALUES ('log_auto_clean', '1');
INSERT INTO `config` VALUES ('log_db_threshold', '30000');
INSERT INTO `config` VALUES ('log_space_threshold', '15%');
INSERT INTO `config` VALUES ('log_syslog', '0');
INSERT INTO `config` VALUES ('mail_path', '/usr/bin/mail');
INSERT INTO `config` VALUES ('max_event_tmp', '10000');
INSERT INTO `config` VALUES ('md5_salt', 'salty_dog');
INSERT INTO `config` VALUES ('mrtg_path', '/usr/bin/');
INSERT INTO `config` VALUES ('mrtg_rrd_files_path', '/var/lib/ossim/rrd/');
INSERT INTO `config` VALUES ('nagios_cfgs', '/etc/nagios3/conf.d/ossim-configs/');
INSERT INTO `config` VALUES ('nagios_link', '/nagios3/');
INSERT INTO `config` VALUES ('nagios_reload_cmd', '/etc/init.d/nagios3 reload || { /etc/init.d/nagios3 stop;/etc/init.d/nagios3 start; }');
INSERT INTO `config` VALUES ('nedi_autodiscovery', '0');
INSERT INTO `config` VALUES ('nessusrc_path', '/work/web/ossim/www/vulnmeter/tmp/.nessusrc');
INSERT INTO `config` VALUES ('nessus_admin_pass', 'BØ¦ZKþƒìÉ‰&ÞX«óˆâª~[˜{€«\\\Z\\');
INSERT INTO `config` VALUES ('nessus_admin_user', 'ovas-super-admin');
INSERT INTO `config` VALUES ('nessus_distributed', '0');
INSERT INTO `config` VALUES ('nessus_host', '127.0.0.1');
INSERT INTO `config` VALUES ('nessus_pass', 'v!¡@Cíä9mïzö');
INSERT INTO `config` VALUES ('nessus_path', '/usr/bin/omp');
INSERT INTO `config` VALUES ('nessus_port', '9390');
INSERT INTO `config` VALUES ('nessus_pre_scan_locally', '1');
INSERT INTO `config` VALUES ('nessus_rpt_path', '/work/web/ossim/www/vulnmeter/');
INSERT INTO `config` VALUES ('nessus_updater_path', '/usr/sbin/openvas-nvt-sync');
INSERT INTO `config` VALUES ('nessus_user', 'ossim');
INSERT INTO `config` VALUES ('netflow', '45');
INSERT INTO `config` VALUES ('network_auto_discovery', '0');
INSERT INTO `config` VALUES ('network_topology_chart', '8082');
INSERT INTO `config` VALUES ('nfsen_in_frame', '0');
INSERT INTO `config` VALUES ('nmap_path', '/usr/bin/nmap');
INSERT INTO `config` VALUES ('ocs_link', '/ossim/ocsreports/index.php?lang=english');
INSERT INTO `config` VALUES ('ossim_link', '/ossim/');
INSERT INTO `config` VALUES ('ossim_schema_version', '5.6.0');
INSERT INTO `config` VALUES ('ossim_server_version', '5.6.free.commit:custom_build-no-git-commit');
INSERT INTO `config` VALUES ('ossim_web_pass', '');
INSERT INTO `config` VALUES ('ossim_web_user', '');
INSERT INTO `config` VALUES ('osvdb_base', 'osvdb');
INSERT INTO `config` VALUES ('osvdb_host', '127.0.0.1');
INSERT INTO `config` VALUES ('osvdb_pass', '\\zae]òKF’oóŸ‰—¸e');
INSERT INTO `config` VALUES ('osvdb_type', 'mysql');
INSERT INTO `config` VALUES ('osvdb_user', 'root');
INSERT INTO `config` VALUES ('ovcp_link', '');
INSERT INTO `config` VALUES ('p0f_path', '/usr/sbin/p0f');
INSERT INTO `config` VALUES ('panel_configs_dir', '/work/etc/ossim/framework/panel/configs');
INSERT INTO `config` VALUES ('panel_plugins_dir', '');
INSERT INTO `config` VALUES ('pass_complex', 'no');
INSERT INTO `config` VALUES ('pass_expire', '0');
INSERT INTO `config` VALUES ('pass_expire_min', '0');
INSERT INTO `config` VALUES ('pass_history', '0');
INSERT INTO `config` VALUES ('pass_length_max', '255');
INSERT INTO `config` VALUES ('pass_length_min', '7');
INSERT INTO `config` VALUES ('phpgacl_base', 'ossim_acl');
INSERT INTO `config` VALUES ('phpgacl_host', '127.0.0.1');
INSERT INTO `config` VALUES ('phpgacl_pass', '\\zae]òKF’oóŸ‰—¸e');
INSERT INTO `config` VALUES ('phpgacl_path', '/usr/share/phpgacl/');
INSERT INTO `config` VALUES ('phpgacl_type', 'mysql');
INSERT INTO `config` VALUES ('phpgacl_user', 'root');
INSERT INTO `config` VALUES ('proxy_password', '');
INSERT INTO `config` VALUES ('proxy_url', '');
INSERT INTO `config` VALUES ('proxy_user', '');
INSERT INTO `config` VALUES ('recovery', '1');
INSERT INTO `config` VALUES ('remote_key', '');
INSERT INTO `config` VALUES ('report_graph_type', 'images');
INSERT INTO `config` VALUES ('repository_upload_dir', '/work/web/ossim/uploads');
INSERT INTO `config` VALUES ('request_ip', '19.19.19.197,19.19.19.198');
INSERT INTO `config` VALUES ('rrdpath_stats', '/var/lib/ossim/rrd/event_stats/');
INSERT INTO `config` VALUES ('rrdtool_lib_path', '/usr/lib/perl5/');
INSERT INTO `config` VALUES ('rrdtool_path', '/usr/bin/');
INSERT INTO `config` VALUES ('scanner_type', 'openvas3omp');
INSERT INTO `config` VALUES ('SensorProtocol', 'UDP');
INSERT INTO `config` VALUES ('server_address', '127.0.0.1');
INSERT INTO `config` VALUES ('server_alarms_to_syslog', 'yes');
INSERT INTO `config` VALUES ('server_correlate', 'yes');
INSERT INTO `config` VALUES ('server_cross_correlate', 'yes');
INSERT INTO `config` VALUES ('server_forward_alarm', 'yes');
INSERT INTO `config` VALUES ('server_forward_event', 'yes');
INSERT INTO `config` VALUES ('server_id', '4288fff5-e387-4b12-B01D-450538c1458e');
INSERT INTO `config` VALUES ('server_logger_if_priority', NULL);
INSERT INTO `config` VALUES ('server_port', '40001');
INSERT INTO `config` VALUES ('server_qualify', 'yes');
INSERT INTO `config` VALUES ('server_remote_logger', 'no');
INSERT INTO `config` VALUES ('server_remote_logger_ossim_url', '');
INSERT INTO `config` VALUES ('server_remote_logger_pass', '');
INSERT INTO `config` VALUES ('server_remote_logger_user', '');
INSERT INTO `config` VALUES ('server_reputation', 'no');
INSERT INTO `config` VALUES ('server_resend_alarm', '1');
INSERT INTO `config` VALUES ('server_resend_event', '1');
INSERT INTO `config` VALUES ('server_sem', 'yes');
INSERT INTO `config` VALUES ('server_sign', 'no');
INSERT INTO `config` VALUES ('server_sim', 'yes');
INSERT INTO `config` VALUES ('server_store', 'yes');
INSERT INTO `config` VALUES ('session_timeout', '15');
INSERT INTO `config` VALUES ('set_backup', '1');
INSERT INTO `config` VALUES ('smtp_pass', '');
INSERT INTO `config` VALUES ('smtp_port', '25');
INSERT INTO `config` VALUES ('smtp_server_address', '127.0.0.1');
INSERT INTO `config` VALUES ('smtp_user', '');
INSERT INTO `config` VALUES ('snmp_comm', '');
INSERT INTO `config` VALUES ('snort_base', 'alienvault_siem');
INSERT INTO `config` VALUES ('snort_host', '127.0.0.1');
INSERT INTO `config` VALUES ('snort_pass', '\\zae]òKF’oóŸ‰—¸e');
INSERT INTO `config` VALUES ('snort_path', '/etc/snort/');
INSERT INTO `config` VALUES ('snort_port', '3306');
INSERT INTO `config` VALUES ('snort_rules_path', '/etc/snort/rules/');
INSERT INTO `config` VALUES ('snort_type', 'mysql');
INSERT INTO `config` VALUES ('snort_user', 'root');
INSERT INTO `config` VALUES ('solera_enable', '0');
INSERT INTO `config` VALUES ('start_welcome_wizard', '0');
INSERT INTO `config` VALUES ('storage_type', '3');
INSERT INTO `config` VALUES ('strategy_ips', '');
INSERT INTO `config` VALUES ('strategy_num', '100');
INSERT INTO `config` VALUES ('strategy_time', '09:00:00~12:00:00');
INSERT INTO `config` VALUES ('system_status_chart', '/app/kibana#/dashboard/548200a0-8508-11ea-b620-6d05361d3f6a?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-15m%2Cto%3Anow))');
INSERT INTO `config` VALUES ('tclevent_chart', '/app/kibana/app/kibana#/dashboard/32133720-112e-11eb-a9e8-cde36546f806?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-30d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('tcp_max_download', '0');
INSERT INTO `config` VALUES ('tcp_max_upload', '0');
INSERT INTO `config` VALUES ('threat_intelligence_chart', '/app/kibana#/dashboard/4d498520-34de-11eb-b22e-919ef18b779b?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-1d%2Cto%3Anow))');
INSERT INTO `config` VALUES ('threshold', '30');
INSERT INTO `config` VALUES ('tickets_max_days', '15');
INSERT INTO `config` VALUES ('tickets_send_mail', 'no');
INSERT INTO `config` VALUES ('tickets_template_link', NULL);
INSERT INTO `config` VALUES ('time_out', '30');
INSERT INTO `config` VALUES ('touch_path', '/bin/tail');
INSERT INTO `config` VALUES ('track_usage_information', '0');
INSERT INTO `config` VALUES ('udp_max_download', '0');
INSERT INTO `config` VALUES ('udp_max_upload', '0');
INSERT INTO `config` VALUES ('unlock_user_interval', '5');
INSERT INTO `config` VALUES ('update_checks_enable', 'yes');
INSERT INTO `config` VALUES ('update_checks_pro_source', 'http://data.alienvault.com/updates/update_log_pro.txt');
INSERT INTO `config` VALUES ('update_checks_source', 'http://data.alienvault.com/updates/update_log.txt');
INSERT INTO `config` VALUES ('update_checks_use_proxy', 'no');
INSERT INTO `config` VALUES ('user_action_log', '1');
INSERT INTO `config` VALUES ('user_life_time', '');
INSERT INTO `config` VALUES ('use_resolv', '0');
INSERT INTO `config` VALUES ('use_ssl', 'no');
INSERT INTO `config` VALUES ('use_svg_graphics', '0');
INSERT INTO `config` VALUES ('vulnerability_incident_threshold', '2');
INSERT INTO `config` VALUES ('welcome_wizard_date', '1537866685');
INSERT INTO `config` VALUES ('wget_path', '/usr/bin/wget');

SET FOREIGN_KEY_CHECKS = 1;
