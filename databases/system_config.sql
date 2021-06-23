/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.70
Source Server Version : 50647
Source Host           : 19.19.19.70:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-12-14 04:28:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for system_config
-- ----------------------------
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL COMMENT '配置名称',
  `value` varchar(1000) DEFAULT NULL COMMENT '配置内容',
  `desc` varchar(1000) DEFAULT NULL COMMENT '配置描述',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '配置状态0关闭1开启',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型：1系统配置2用户配置',
  `user_id` mediumint(5) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统配置表';

-- ----------------------------
-- Records of system_config
-- ----------------------------
INSERT INTO `system_config` VALUES ('1', 'ES_SHOW_TIME', '900', 'es相关的数据展示时间', '1', '1', '0');
INSERT INTO `system_config` VALUES ('2', 'ES_DATA_POLICY', '{\"index\":\"data_flow*\",\"saveDays\":\"180\",\"openDays\":\"3\",\"isOpen\":\"true\"},{\"index\":\"data_event*\",\"saveDays\":\"180\",\"openDays\":\"3\",\"isOpen\":\"true\"},{\"index\":\"threatip*\",\"saveDays\":\"180\",\"openDays\":\"3\",\"isOpen\":\"true\"},{\"index\":\"threaturl*\",\"saveDays\":\"180\",\"openDays\":\"3\",\"isOpen\":\"true\"},{\"index\":\"threatcer*\",\"saveDays\":\"180\",\"openDays\":\"3\",\"isOpen\":\"true\"}', 'index:索引类型,saveDays:索引存储天数,openDays:在线索引数量,isOpen:配置是否启用', '1', '1', '0');
INSERT INTO `system_config` VALUES ('3', 'SPARK_DATA_EXPORT', '{\"id\":\"3\",\"db\":\"es\",\"index\":\"data_event\",\"table\":\"\",\"field\":\"\",\"label\":\"\\u544a', 'spark程序结果输出关系映射', '1', '1', '0');
INSERT INTO `system_config` VALUES ('7', 'PLATFORM_CHECK_INFO', '{\"assetType\":88,\"isCheck\":1,\"isDetermine\":1};{\"assetType\":89,\"isCheck\":1,\"isDetermine\":0};{\"assetType\":90,\"isCheck\":1,\"isDetermine\":0};{\"assetType\":91,\"isCheck\":1,\"isDetermine\":0};{\"assetType\":92,\"isCheck\":1,\"isDetermine\":0}', '根据探针类型判断对探针是否检测和判断', '1', '1', '0');
INSERT INTO `system_config` VALUES ('4', 'PLATFROM_TITLE', '', '登录页主标题，最长输入6个字符(由于服务器缓存原因数据更新需等待5分钟左右)', '1', '1', '0');
INSERT INTO `system_config` VALUES ('5', 'SUB_PLATFROM_TITLE', '工控统一安全管理平台', '登录页副标题(由于服务器缓存原因数据更新需等待5分钟左右)', '1', '1', '0');
INSERT INTO `system_config` VALUES ('6', 'DEFAULT_ROULTER', '/home-situation', null, '1', '2', '2');
INSERT INTO `system_config` VALUES ('8', 'PLATFORM_CHECK_MODE', '2', '版本默认检测刷新\n0 :  不检测心跳\n1 ： 只检测心跳\n2 ： 先检测心跳，再正常检测\n(时间参数刷新频率为1min)', '1', '1', '0');
INSERT INTO `system_config` VALUES ('9', 'SPARK_DATA_ACCESS', '1', '0 : 对数据进行管理过滤\n1 ：不对数据进行管理', '1', '1', '0');
INSERT INTO `system_config` VALUES ('10', 'DEFAULT_ROULTER', '/home-situation', null, '1', '2', '1');
INSERT INTO `system_config` VALUES ('11', 'ES_HOSTNAME', '127.0.0.1:9200', 'ES集群主机名', '1', '1', '0');
