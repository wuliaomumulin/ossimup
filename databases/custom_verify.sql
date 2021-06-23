/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 15:17:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for custom_verify
-- ----------------------------
DROP TABLE IF EXISTS `custom_verify`;
CREATE TABLE `custom_verify` (
  `attribute` varchar(255) CHARACTER SET utf8 NOT NULL,
  `value` varchar(4096) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`attribute`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_croatian_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of custom_verify
-- ----------------------------
INSERT INTO `custom_verify` VALUES ('', null);
INSERT INTO `custom_verify` VALUES ('accept_time', '2020-04-05');
INSERT INTO `custom_verify` VALUES ('address', '大同路');
INSERT INTO `custom_verify` VALUES ('addressDetail', '110000,110100,110101');
INSERT INTO `custom_verify` VALUES ('channel_num', '123111');
INSERT INTO `custom_verify` VALUES ('city', '市辖区');
INSERT INTO `custom_verify` VALUES ('cityCode', '110100');
INSERT INTO `custom_verify` VALUES ('company', '安帝科技');
INSERT INTO `custom_verify` VALUES ('contact', '王');
INSERT INTO `custom_verify` VALUES ('county', '东城区');
INSERT INTO `custom_verify` VALUES ('countyCode', '110101');
INSERT INTO `custom_verify` VALUES ('equ_num', '123123123');
INSERT INTO `custom_verify` VALUES ('factory', '北京风电厂');
INSERT INTO `custom_verify` VALUES ('factory_person', 'test');
INSERT INTO `custom_verify` VALUES ('factory_phone', '18856565659');
INSERT INTO `custom_verify` VALUES ('factory_type', '水光电');
INSERT INTO `custom_verify` VALUES ('isp', '移动');
INSERT INTO `custom_verify` VALUES ('lat', '39.58');
INSERT INTO `custom_verify` VALUES ('lng', '116.18');
INSERT INTO `custom_verify` VALUES ('memo', '1111');
INSERT INTO `custom_verify` VALUES ('nick_name', '北风');
INSERT INTO `custom_verify` VALUES ('province', '北京');
INSERT INTO `custom_verify` VALUES ('provinceCode', '110000');
INSERT INTO `custom_verify` VALUES ('sim_num', '123');
INSERT INTO `custom_verify` VALUES ('telphone', '15545454545');
