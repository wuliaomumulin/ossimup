/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 14:51:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for zhny_smart_collect
-- ----------------------------
DROP TABLE IF EXISTS `zhny_smart_collect`;
CREATE TABLE `zhny_smart_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` varchar(32) NOT NULL DEFAULT 'MAGUS' COMMENT '固定标注厂商字段',
  `dev` varchar(8) DEFAULT NULL COMMENT '整数字符串，表示机组编号',
  `ts` timestamp NULL DEFAULT NULL COMMENT '时间戳',
  `tag` varchar(32) DEFAULT NULL COMMENT '数据标签',
  `value` varchar(16) DEFAULT NULL COMMENT '数据标签对应的值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=563 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhny_smart_collect
-- ----------------------------
INSERT INTO `zhny_smart_collect` VALUES ('539', 'MAGUS', '0', '2020-05-07 10:41:23', 'FDL', '37');
INSERT INTO `zhny_smart_collect` VALUES ('540', 'MAGUS', '1', '2020-05-07 10:41:23', 'FDSBLYXS', '423');
INSERT INTO `zhny_smart_collect` VALUES ('541', 'MAGUS', '0', '2020-05-07 10:41:23', 'GRL', '37');
INSERT INTO `zhny_smart_collect` VALUES ('542', 'MAGUS', '0', '2020-05-07 10:41:23', 'YXRL', '37');
INSERT INTO `zhny_smart_collect` VALUES ('543', 'MAGUS', '1', '2020-05-07 10:41:23', 'JHFDL', '1');
INSERT INTO `zhny_smart_collect` VALUES ('544', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWSO2', '1');
INSERT INTO `zhny_smart_collect` VALUES ('545', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWNO', '1');
INSERT INTO `zhny_smart_collect` VALUES ('546', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWYC', '1');
INSERT INTO `zhny_smart_collect` VALUES ('547', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWJXSO2', '1');
INSERT INTO `zhny_smart_collect` VALUES ('548', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWJXNO', '1');
INSERT INTO `zhny_smart_collect` VALUES ('549', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWJXYC', '1');
INSERT INTO `zhny_smart_collect` VALUES ('550', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWNDSO2', '1');
INSERT INTO `zhny_smart_collect` VALUES ('551', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWNDNO', '1');
INSERT INTO `zhny_smart_collect` VALUES ('552', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWNDYC', '1');
INSERT INTO `zhny_smart_collect` VALUES ('553', 'MAGUS', '1', '2020-05-07 10:41:23', 'WRWJPCO2', '1');
INSERT INTO `zhny_smart_collect` VALUES ('554', 'MAGUS', '1', '2020-05-07 10:41:23', 'GDBZMH', '1');
INSERT INTO `zhny_smart_collect` VALUES ('555', 'MAGUS', '1', '2020-05-07 10:41:23', 'FDBZML', '1');
INSERT INTO `zhny_smart_collect` VALUES ('556', 'MAGUS', '1', '2020-05-07 10:41:23', 'FDSCCYDL', '1');
INSERT INTO `zhny_smart_collect` VALUES ('557', 'MAGUS', '1', '2020-05-07 10:41:23', 'HJWD', '1');
INSERT INTO `zhny_smart_collect` VALUES ('558', 'MAGUS', '1', '2020-05-07 10:41:23', 'HJFS', '1');
INSERT INTO `zhny_smart_collect` VALUES ('559', 'MAGUS', '1', '2020-05-07 10:41:23', 'HJGL', '1');
INSERT INTO `zhny_smart_collect` VALUES ('560', 'MAGUS', '1', '2020-07-06 16:16:38', 'FDL', '37');
INSERT INTO `zhny_smart_collect` VALUES ('561', 'MAGUS', '1', '2020-07-06 16:16:38', 'GRL', '37');
INSERT INTO `zhny_smart_collect` VALUES ('562', 'MAGUS', '1', '2020-07-06 16:16:38', 'YXRL', '37');
