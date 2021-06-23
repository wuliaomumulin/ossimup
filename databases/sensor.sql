/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-08-15 20:03:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sensor
-- ----------------------------
DROP TABLE IF EXISTS `sensor`;
CREATE TABLE `sensor` (
  `id` binary(16) NOT NULL,
  `name` varchar(64) NOT NULL,
  `ip` varbinary(16) DEFAULT NULL,
  `priority` smallint(6) NOT NULL,
  `port` int(11) NOT NULL,
  `connect` smallint(6) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `tzone` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of sensor
-- ----------------------------
INSERT INTO `sensor` VALUES (0x320C77328BD711EA90860013847D872C, '1', 0x7F000001, '5', '40001', '0', '', '8');
