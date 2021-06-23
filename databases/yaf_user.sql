/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.70
Source Server Version : 50647
Source Host           : 19.19.19.70:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-12-12 14:02:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for yaf_user
-- ----------------------------
DROP TABLE IF EXISTS `yaf_user`;
CREATE TABLE `yaf_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '组别ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `usb_key` varchar(100) NOT NULL DEFAULT '' COMMENT 'USB_KEY码',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `logintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `loginip` varchar(50) NOT NULL DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0|禁用，1|启用',
  `rid` int(10) DEFAULT NULL COMMENT '角色id',
  `user_attrs` varchar(50) NOT NULL COMMENT '用户扩展字段',
  `comid` mediumint(5) unsigned NOT NULL DEFAULT '0' COMMENT '所属公司ID',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `username` (`username`) USING BTREE,
  KEY `email` (`email`) USING BTREE,
  KEY `mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='会员表';

-- ----------------------------
-- Records of yaf_user
-- ----------------------------
INSERT INTO `yaf_user` VALUES ('1', '0', 'secadmin', '超级管理员', '56e1af2b5764fc7519842a5b7f9a2c55', '', 'secsdmin@163.com', '15555555555', '', '0', '0', '2019-08-09', 'oO9kN7xO', '0', UNIX_TIMESTAMP(NOW()), '19.19.19.143', '0',UNIX_TIMESTAMP(NOW()),UNIX_TIMESTAMP(NOW()), '1', '1', '{\"theme\": \"theme2\"}', '0');
INSERT INTO `yaf_user` VALUES ('2', '0', 'admin', '管理员', '6f637ee9b9273bee20674d7441bd1e69', '', 'admin@163.com', '15210910111', '', '0', '0', '2019-08-09', 'oO9kN7xO', '0', UNIX_TIMESTAMP(NOW()), '19.19.19.143', '0',UNIX_TIMESTAMP(NOW()),UNIX_TIMESTAMP(NOW()), '1', '2', 'null', '0');
INSERT INTO `yaf_user` VALUES ('3', '0', 'audit', '审计员', '6f637ee9b9273bee20674d7441bd1e69', '', 'audit@163.com', '15210910111', '', '0', '0', '2019-08-09', 'oO9kN7xO', '0', UNIX_TIMESTAMP(NOW()), '19.19.19.143', '0',UNIX_TIMESTAMP(NOW()),UNIX_TIMESTAMP(NOW()), '1', '4', 'null', '0');
INSERT INTO `yaf_user` VALUES ('4', '0', 'operator', '操作员', '6f637ee9b9273bee20674d7441bd1e69', '', 'operator@163.com', '15101013131', '', '0', '0', '2020-06-05', 'oO9kN7xO', '0', UNIX_TIMESTAMP(NOW()), '19.19.19.143', '0',UNIX_TIMESTAMP(NOW()),UNIX_TIMESTAMP(NOW()), '1', '3', '', '0');
-- 中烟项目
INSERT INTO `yaf_user` VALUES ('5', '0', 'zsadmin', '制丝', 'ee0dc17591672f57ca64cf5a67f00399', '', 'zaadmin@163.com', '18811112222', '', '0', '0', '2020-10-13', '', '0', '1602666570', '19.19.19.149', '0', '1602595935', '0', '1', '2', '{\"device\":\"1102\",\"monitor\":\"1202\"}', '0');
INSERT INTO `yaf_user` VALUES ('6', '0', 'jbadmin', '卷包', '6f637ee9b9273bee20674d7441bd1e69', '', 'jbadmin@163.com', '18811112221', '', '0', '0', '2020-10-13', '', '0', '1607526839', '19.19.19.143', '0', '1602595935', '0', '1', '2', '{\"device\":\"1101\",\"monitor\":\"1201\"}', '0');
INSERT INTO `yaf_user` VALUES ('7', '0', 'ngadmin', '能管', '6f637ee9b9273bee20674d7441bd1e69', '', 'jbadmin@163.com', '18811112223', '', '0', '0', '2020-10-13', '', '0', '1607718779', '19.19.19.143', '0', '1602595935', '0', '1', '2', '{\"device\":\"1103\",\"monitor\":\"1203\"}', '0');
