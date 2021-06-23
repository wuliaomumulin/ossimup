/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 15:13:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sys_log
-- ----------------------------
DROP TABLE IF EXISTS `sys_log`;
CREATE TABLE `sys_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `user_name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `log_event` varchar(32) NOT NULL DEFAULT '' COMMENT '日志动作',
  `log_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '用户ip',
  `remark` varchar(2048) NOT NULL DEFAULT '' COMMENT '备注',
  `performStartTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '触发时间',
  `operation_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '插入时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后插入时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `IndexOptTime` (`operation_time`) USING BTREE,
  KEY `IndexUserName` (`user_name`) USING BTREE,
  KEY `IndexEvent` (`log_event`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20418 DEFAULT CHARSET=utf8mb4 COMMENT='系统日志';
