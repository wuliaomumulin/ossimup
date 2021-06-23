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

 Date: 03/12/2020 15:09:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for hids_agents
-- ----------------------------
DROP TABLE IF EXISTS `hids_agents`;
CREATE TABLE `hids_agents`  (
  `sensor_id` binary(16) NOT NULL,
  `agent_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `agent_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `agent_ip` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `agent_status` tinyint(1) NULL DEFAULT NULL,
  `host_id` binary(16) NULL DEFAULT NULL,
  PRIMARY KEY (`sensor_id`, `agent_id`) USING BTREE,
  INDEX `status`(`agent_status`) USING BTREE,
  INDEX `host_id`(`host_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of hids_agents
-- ----------------------------


SET FOREIGN_KEY_CHECKS = 1;
