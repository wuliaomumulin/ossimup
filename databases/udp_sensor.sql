/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 14:51:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for udp_sensor
-- ----------------------------
DROP TABLE IF EXISTS `udp_sensor`;
CREATE TABLE `udp_sensor` (
  `host_id` char(32) COLLATE utf8mb4_bin NOT NULL,
  `ip` char(15) COLLATE utf8mb4_bin NOT NULL COMMENT 'udp采集器ip',
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '采集器名称',
  `report_id` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '采集器上报的sensor_id',
  `sensor_id` varchar(50) COLLATE utf8mb4_bin NOT NULL DEFAULT 'NOTFOUND' COMMENT '采集器的唯一标识',
  `online_status` tinyint(1) DEFAULT '0' COMMENT '在线状态',
  `collect_status` tinyint(1) DEFAULT '0' COMMENT '采集状态',
  `traffic_status` tinyint(1) DEFAULT '0' COMMENT '流量状态',
  `switch_status` tinyint(1) DEFAULT '0' COMMENT '交换机采集状态',
  `agent_status` tinyint(1) DEFAULT '0' COMMENT 'Agent采集状态',
  `backup_status` tinyint(1) DEFAULT '0' COMMENT '备平台状态',
  `cpu` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'cpu信息',
  `mem` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '内存',
  `contact` char(80) COLLATE utf8mb4_bin DEFAULT '' COMMENT '负责人',
  `disk` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '磁盘',
  `ver` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '版本',
  `descr` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '描述',
  `type` int(10) DEFAULT '11' COMMENT '分类',
  `subtype` int(10) DEFAULT '0' COMMENT '子类',
  `port` int(10) DEFAULT '8801' COMMENT '端口',
  `ctime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='udp采集器信息上报表';

-- ----------------------------
-- Records of udp_sensor
-- ----------------------------
