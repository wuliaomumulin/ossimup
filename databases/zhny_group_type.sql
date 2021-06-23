/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-27 16:50:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for zhny_group_type
-- ----------------------------
DROP TABLE IF EXISTS `zhny_group_type`;
CREATE TABLE `zhny_group_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhny_group_type
-- ----------------------------
INSERT INTO `zhny_group_type` VALUES ('1', '水电');
INSERT INTO `zhny_group_type` VALUES ('4', '风电');
INSERT INTO `zhny_group_type` VALUES ('5', '光伏');
INSERT INTO `zhny_group_type` VALUES ('6', '核电');
INSERT INTO `zhny_group_type` VALUES ('7', '火电');
INSERT INTO `zhny_group_type` VALUES ('8', '厂级指标');
