/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.70
Source Server Version : 50647
Source Host           : 19.19.19.70:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-12-01 14:18:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for alarm
-- ----------------------------
alter table alarm add(`is_read` int(1) DEFAULT '0' COMMENT '是否确认')