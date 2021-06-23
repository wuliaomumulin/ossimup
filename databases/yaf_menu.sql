/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.70
Source Server Version : 50647
Source Host           : 19.19.19.70:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-12-12 13:32:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for yaf_menu
-- ----------------------------
DROP TABLE IF EXISTS `yaf_menu`;
CREATE TABLE `yaf_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL DEFAULT '',
  `name_en` char(50) NOT NULL DEFAULT '',
  `pid` int(10) DEFAULT NULL,
  `router` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `name_admin` char(50) NOT NULL DEFAULT '',
  `priotity` tinyint(1) DEFAULT NULL COMMENT '优先级',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='导航';

-- ----------------------------
-- Records of yaf_menu
-- ----------------------------
INSERT INTO `yaf_menu` VALUES ('1', '安全态势', 'Home', '0', '/home-situation', 'el-icon-s-home', '安全态势', '1');
INSERT INTO `yaf_menu` VALUES ('2', '告警事件', 'Alarm', '0', '/alarm-events', 'fa fa-warning', '告警管理', '2');
INSERT INTO `yaf_menu` VALUES ('3', '安全事件', 'SIEM', '0', '/siem-events', 'el-icon-warning', '安全事件', '3');
INSERT INTO `yaf_menu` VALUES ('4', '资产管理', 'Assets', '0', '/assets', 'fa fa-cubes', '资产管理', '4');
INSERT INTO `yaf_menu` VALUES ('5', '统计报表', 'Report', '0', '/report-manager', 'fa fa-area-chart', '统计报表', '5');
INSERT INTO `yaf_menu` VALUES ('6', '部署状态', 'Deployment', '0', '/deployment-firewall', 'fa fa-flag', '部署状态', '6');
INSERT INTO `yaf_menu` VALUES ('7', '威胁管理', 'Threat', '0', '/threat-rules', 'fa fa-ticket', '威胁管理', '7');
INSERT INTO `yaf_menu` VALUES ('8', '系统配置', 'Configuration', '0', '/configuration_system', 'fa fa-cogs', '系统配置', '8');
INSERT INTO `yaf_menu` VALUES ('9', '安全态势', 'Situation', '1', '/home-situation', '', '安全态势', null);
INSERT INTO `yaf_menu` VALUES ('10', '告警事件', 'Alarm', '2', '/alarm-events', '', '告警事件', null);
INSERT INTO `yaf_menu` VALUES ('12', '实时事件', 'SIEM', '3', '/siem-events', '', '安全事件', '1');
INSERT INTO `yaf_menu` VALUES ('14', '主机资产', 'Assets', '4', '/assets-manager', '', '主机资产', '1');
INSERT INTO `yaf_menu` VALUES ('15', '资产拓扑', 'Topology', '4', '/topology/index', '', '资产拓扑', '4');
INSERT INTO `yaf_menu` VALUES ('17', '采集状态', 'Agent', '6', '/deployment-agent', '', '采集状态', '12');
INSERT INTO `yaf_menu` VALUES ('19', '系统状态', 'Status', '6', '/deployment-system', '', '系统状态', '11');
INSERT INTO `yaf_menu` VALUES ('20', '告警规则', 'Rules', '7', '/threat-rules', '', '告警规则', '4');
INSERT INTO `yaf_menu` VALUES ('21', '事件管理', 'Plugins', '7', '/threat-plugins', '', '事件管理', '2');
INSERT INTO `yaf_menu` VALUES ('23', '系统配置', 'Configuration', '8', '/configuration-system', '', '系统配置', null);
INSERT INTO `yaf_menu` VALUES ('24', '实施配置', 'Implement', '8', '/configuration-implement', '', '实施配置', null);
INSERT INTO `yaf_menu` VALUES ('25', '备份还原', 'Backup', '8', '/configuration-backup', '', '备份还原', null);
INSERT INTO `yaf_menu` VALUES ('26', '用户管理', 'Users', '27', '/user-management', '', '用户管理', null);
INSERT INTO `yaf_menu` VALUES ('27', '用户管理', 'Users', '0', '/user-management', 'fa fa-user-plus', '用户管理', '9');
INSERT INTO `yaf_menu` VALUES ('28', '审计日志', 'Logs', '0', '/edit-log', 'fa fa-calendar', '审计日志', '10');
INSERT INTO `yaf_menu` VALUES ('29', '审计日志', 'Logs', '28', '/edit-log', '', '审计日志', null);
INSERT INTO `yaf_menu` VALUES ('30', '事件脚本', 'Edit plugins', '7', '/edit-plugins', null, '事件脚本', '3');
INSERT INTO `yaf_menu` VALUES ('31', '权限管理', 'user group', '27', '/user-group', '', '权限管理', null);
INSERT INTO `yaf_menu` VALUES ('32', '审计策略', 'audit strategy', '27', '/audit-strategy', null, '审计策略', null);
INSERT INTO `yaf_menu` VALUES ('33', '角色管理', 'role management', '27', '/role-management', null, '角色管理', null);
INSERT INTO `yaf_menu` VALUES ('42', '设备列表', 'Device list', '6', '/device-list', null, '设备列表', null);
INSERT INTO `yaf_menu` VALUES ('43', '主机卫士', 'Node list', '6', '/node-list', null, '主机卫士', null);
INSERT INTO `yaf_menu` VALUES ('44', '监测审计', 'Monitoring audit', '6', '/audit-list', null, '监测审计', null);
INSERT INTO `yaf_menu` VALUES ('45', '资产脆弱性', 'Assest vulnerable', '4', '/assest-vulnerable', null, '资产脆弱性', '3');
INSERT INTO `yaf_menu` VALUES ('47', '网络连接拓扑', 'Network sersec', '4', '/network-topology-sec', null, '网路拓扑', '5');
INSERT INTO `yaf_menu` VALUES ('50', '主机事件', 'Host Event', '3', '/host-event', null, '主机事件', '3');
INSERT INTO `yaf_menu` VALUES ('51', '历史事件', 'History siem events', '3', '/history-siem-events', null, '历史安全事件', '2');
INSERT INTO `yaf_menu` VALUES ('52', '工控事件', 'Tlc Event', '3', '/tlc-event', null, '工控事件', '5');
INSERT INTO `yaf_menu` VALUES ('53', '威胁情报', 'Threat intelligence', '2', '/threat-intell', null, '威胁情报', null);
INSERT INTO `yaf_menu` VALUES ('54', '网络资产', 'Network Asset', '4', '/network-asset', null, '网络资产', '2');
-- 电科院
-- INSERT INTO `yaf_menu` VALUES ('48', '统计报表', 'report manager new', '5', '/report-manager-new', null, '统计报表', null);
-- INSERT INTO `yaf_menu` VALUES ('55', '事件统计', 'siem statistics', '3', '/siem-statistics-new', null, '事件统计', null);

-- 中烟项目
INSERT INTO `yaf_menu` VALUES ('13', '事件统计', 'SIEM statistics', '3', '/siem-statistics', '', '事件统计', '6');
INSERT INTO `yaf_menu` VALUES ('16', '统计报表', 'Report', '5', '/report-manager', '', '统计报表', null);