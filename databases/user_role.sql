/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.70
Source Server Version : 50647
Source Host           : 19.19.19.70:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-12-09 15:21:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rolename` char(50) DEFAULT NULL,
  `rank` varchar(255) DEFAULT NULL,
  `isdel` tinyint(1) DEFAULT '1' COMMENT '0:不可删除，1可删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of user_role
-- ----------------------------
INSERT INTO `user_role` VALUES ('1', '超级管理员', '1,9,10,12,13,14,15,5,16,6,42,43,44,19,17,7,21,30,20,8,24,25,23,27,26,31,32,33,28,29,2,3,4', '0');
INSERT INTO `user_role` VALUES ('2', '管理员', '1,9,2,11,10,3,13,12,5,16,6,19,17,18,7,22,21,30,20,23,24,25,32,26,28,29,8,27', '0');
INSERT INTO `user_role` VALUES ('3', '操作员', '4,15,14', '0');
INSERT INTO `user_role` VALUES ('4', '审计员', '2,11,10,28,29', '0');
