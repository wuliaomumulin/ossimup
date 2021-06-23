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

 Date: 04/01/2021 16:49:36
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sys_log_type
-- ----------------------------
DROP TABLE IF EXISTS `sys_log_type`;
CREATE TABLE `sys_log_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `menu_id` int(11) NULL DEFAULT NULL COMMENT '功能menu ID',
  `status` int(5) NULL DEFAULT 1 COMMENT '1 启用  0禁用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of sys_log_type
-- ----------------------------
INSERT INTO `sys_log_type` VALUES (1, 14, 1);
INSERT INTO `sys_log_type` VALUES (2, 15, 1);
INSERT INTO `sys_log_type` VALUES (4, 20, 0);
INSERT INTO `sys_log_type` VALUES (5, 21, 0);
INSERT INTO `sys_log_type` VALUES (6, 53, 1);
INSERT INTO `sys_log_type` VALUES (7, 24, 1);
INSERT INTO `sys_log_type` VALUES (8, 25, 1);
INSERT INTO `sys_log_type` VALUES (9, 26, 1);
INSERT INTO `sys_log_type` VALUES (10, 30, 1);
INSERT INTO `sys_log_type` VALUES (11, 31, 1);
INSERT INTO `sys_log_type` VALUES (12, 32, 1);
INSERT INTO `sys_log_type` VALUES (13, 23, 1);
INSERT INTO `sys_log_type` VALUES (14, 33, 1);
INSERT INTO `sys_log_type` VALUES (16, 53, 1);
INSERT INTO `sys_log_type` VALUES (17, 55, 1);

SET FOREIGN_KEY_CHECKS = 1;
