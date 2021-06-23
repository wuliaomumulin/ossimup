/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 15:21:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `ctx` binary(16) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('1', 0x00000000000000000000000000000000, '挖掘');
INSERT INTO `category` VALUES ('2', 0x00000000000000000000000000000000, '身份验证');
INSERT INTO `category` VALUES ('3', 0x00000000000000000000000000000000, '访问');
INSERT INTO `category` VALUES ('4', 0x00000000000000000000000000000000, '恶意软件');
INSERT INTO `category` VALUES ('5', 0x00000000000000000000000000000000, '政策');
INSERT INTO `category` VALUES ('6', 0x00000000000000000000000000000000, '拒绝服务');
INSERT INTO `category` VALUES ('7', 0x00000000000000000000000000000000, '可疑');
INSERT INTO `category` VALUES ('8', 0x00000000000000000000000000000000, '网络');
INSERT INTO `category` VALUES ('9', 0x00000000000000000000000000000000, '侦察');
INSERT INTO `category` VALUES ('10', 0x00000000000000000000000000000000, '信息');
INSERT INTO `category` VALUES ('11', 0x00000000000000000000000000000000, '系统');
INSERT INTO `category` VALUES ('12', 0x00000000000000000000000000000000, '杀毒软件');
INSERT INTO `category` VALUES ('13', 0x00000000000000000000000000000000, '应用');
INSERT INTO `category` VALUES ('14', 0x00000000000000000000000000000000, '网络电话');
INSERT INTO `category` VALUES ('15', 0x00000000000000000000000000000000, '消息');
INSERT INTO `category` VALUES ('16', 0x00000000000000000000000000000000, '有效性');
INSERT INTO `category` VALUES ('17', 0x00000000000000000000000000000000, '无线网路');
INSERT INTO `category` VALUES ('18', 0x00000000000000000000000000000000, '库存');
INSERT INTO `category` VALUES ('19', 0x00000000000000000000000000000000, '蜜罐');
INSERT INTO `category` VALUES ('20', 0x00000000000000000000000000000000, '数据库');
INSERT INTO `category` VALUES ('21', 0x00000000000000000000000000000000, '告警');