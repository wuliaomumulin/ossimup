/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.72
Source Server Version : 50647
Source Host           : 19.19.19.72:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-09-14 14:36:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for detection_audit_device
-- ----------------------------
DROP TABLE IF EXISTS `detection_audit_device`;
CREATE TABLE `detection_audit_device` (
  `id` int(16) NOT NULL COMMENT '序号',
  `area` varchar(128) DEFAULT NULL COMMENT '地域组织机构ID',
  `assert_name` varchar(128) DEFAULT NULL COMMENT '设备名称',
  `asset_state` int(1) DEFAULT NULL COMMENT '资产状态',
  `assetsource` varchar(128) DEFAULT NULL COMMENT '资产来源',
  `authtype` varchar(1) DEFAULT NULL COMMENT '认证方式',
  `businesssystem` varchar(128) DEFAULT NULL COMMENT '所属业务系统',
  `csn` varchar(128) DEFAULT NULL COMMENT '序列号',
  `dcdip` varchar(128) DEFAULT NULL COMMENT '采集器IP',
  `devicepurpose` varchar(128) DEFAULT NULL COMMENT '设备用途',
  `devtype` varchar(128) DEFAULT NULL COMMENT '设备类型',
  `encrypttype` int(1) DEFAULT NULL COMMENT '加密方式',
  `hostname` varchar(128) DEFAULT NULL COMMENT '主机名称',
  `innername` varchar(128) DEFAULT NULL COMMENT '内部名称',
  `ipaddra` varchar(128) DEFAULT NULL COMMENT 'IPA地址',
  `ipaddrb` varchar(128) DEFAULT NULL COMMENT 'IPB地址',
  `maca` varchar(128) DEFAULT NULL COMMENT 'macA地址',
  `macb` varchar(128) DEFAULT NULL COMMENT 'macB地址',
  `matching` int(1) DEFAULT NULL COMMENT '匹配状态',
  `model` varchar(128) DEFAULT NULL COMMENT '型号',
  `null` varchar(128) DEFAULT NULL COMMENT '保留一个空字节',
  `ostype` varchar(128) DEFAULT NULL COMMENT '系统版本',
  `person` varchar(128) DEFAULT NULL COMMENT '负责人',
  `physicallocation` varchar(128) DEFAULT NULL COMMENT '物理位置',
  `securityarea` varchar(128) DEFAULT NULL COMMENT '安全分区',
  `seller` varchar(128) DEFAULT NULL COMMENT '厂家名称',
  `snmpread` varchar(128) DEFAULT NULL COMMENT '认证密码',
  `snmptype` varchar(128) DEFAULT NULL COMMENT 'snmp类型',
  `snmpwrite` varchar(128) DEFAULT NULL COMMENT '加密密码',
  `softwareversion` varchar(128) DEFAULT NULL COMMENT '软件版本',
  `stationtype` varchar(128) DEFAULT NULL COMMENT '站点类型',
  `telephone` varchar(128) DEFAULT NULL COMMENT '联系电话',
  `username` varchar(128) DEFAULT NULL COMMENT '用户名',
  `vendorguid` int(128) DEFAULT NULL COMMENT '厂商唯一标识ID',
  `voltagelevel` varchar(128) DEFAULT NULL COMMENT '电压等级',
  `host_id` char(32) NOT NULL COMMENT '探针标识',
  `code` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`code`),
  KEY `key_host_id` (`host_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='检测审计';

-- ----------------------------
-- Records of detection_audit_device
-- ----------------------------
