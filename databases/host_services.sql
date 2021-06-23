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

 Date: 04/12/2020 00:48:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for host_services
-- ----------------------------
DROP TABLE IF EXISTS `host_services`;
CREATE TABLE `host_services`  (
  `host_id` binary(16) NOT NULL,
  `host_ip` varbinary(16) NOT NULL,
  `port` int(11) NOT NULL,
  `protocol` int(11) NOT NULL,
  `service` varchar(10000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `version` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `last_modified` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `source_id` int(11) NULL DEFAULT NULL,
  `nagios` tinyint(1) NULL DEFAULT 0,
  `nagios_status` tinyint(4) NOT NULL DEFAULT 3,
  `tzone` float NULL DEFAULT 0,
  PRIMARY KEY (`host_id`, `host_ip`, `port`, `protocol`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
