/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-27 16:50:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for zhny_dict
-- ----------------------------
DROP TABLE IF EXISTS `zhny_dict`;
CREATE TABLE `zhny_dict` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT '0' COMMENT '父id',
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `attribute` varchar(255) DEFAULT '',
  `unit` char(50) DEFAULT '' COMMENT '单位',
  `type` char(50) DEFAULT '' COMMENT '方式',
  `expire` char(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1527 DEFAULT CHARSET=utf8 COMMENT='系统-字典';

-- ----------------------------
-- Records of zhny_dict
-- ----------------------------
INSERT INTO `zhny_dict` VALUES ('1', '0', '', '基础指标', '', '', '60');
INSERT INTO `zhny_dict` VALUES ('3', '1', 'CYDL', '厂用电量', '万千瓦时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('4', '1', 'ZJRL', '装机容量', '万千瓦', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('7', '0', '', '水情', '', '', '60');
INSERT INTO `zhny_dict` VALUES ('8', '0', '', '可利用率', '', '', '60');
INSERT INTO `zhny_dict` VALUES ('9', '0', '', '燃料情况', '', '', '60');
INSERT INTO `zhny_dict` VALUES ('10', '0', '', '污染物排放', '', '', '60');
INSERT INTO `zhny_dict` VALUES ('11', '0', '', '厂级指标', '', '企业上报', null);
INSERT INTO `zhny_dict` VALUES ('13', '1', '', '损失电量', '万千瓦时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('15', '0', '', '其他指标', '', '', null);
INSERT INTO `zhny_dict` VALUES ('103', '1', 'FDSBLYXS', '发电设备利用小时数', '小时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('104', '1', 'SWDL', '上网电量', '万千瓦时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('105', '1', 'YXRL', '运行容量', '万千瓦', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('106', '1', 'JHFDL', '计划发电量', '万千瓦时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('107', '1', 'GRL', '供热量', '吉焦', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('109', '1', 'FHSNYFDL', '非化石能源发电量', '万千瓦时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('801', '8', 'GFFDDYPJKLYL', '光伏发电单元平均可利用率', '%', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('802', '8', 'FJKLYL', '风机可利用率', '%', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('803', '8', 'GFDZKLYL', '光伏电站可利用率', '%', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('804', '8', 'FDCKLYL', '风电场可利用率', '%', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('902', '9', 'HML', '耗煤量', '吨', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1001', '10', 'WRWSO2', 'SO2排放量', '吨', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1002', '10', 'WRWNO', '氮氧化物排放量', '吨', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1003', '10', 'WRWYC', '烟尘排放量', '吨', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1007', '10', 'WRWNDSO2', 'SO2排放浓度', '毫克/立方', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1008', '10', 'WRWNDNO', '氮氧化物排放浓度', '毫克/立方', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1009', '10', 'WRWNDYC', '烟尘排放浓度', '毫克/立方', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1101', '11', 'YJDJYSRSSGQS', '较大及以上人身事故起数', '起', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1102', '11', 'ESBJDJYSSGQS', '较大及以上设备事故起数', '起', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1103', '11', 'HDCINESEJJYSSJS', '核电厂INES二级及以上事件数', '起', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1105', '11', 'JDJYSDLAQSGQS', '较大及以上电力安全事故起数', '起', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1106', '11', 'JDJYSXXAQSJSL', '较大及以上信息安全事件数量', '起', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1501', '15', 'HJWD', '环境温度', '℃', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1502', '15', 'HJFS', '风速', 'm/s', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1503', '15', 'TYFSL', '太阳辐射量', '兆焦/平方米', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1505', '15', 'WRWJPCO2', '光伏CO2减排量', '吨', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1506', '15', 'WRWJPCO2', '风电CO2减排量', '吨', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1507', '15', 'TTLHCN', '淘汰落后产能', '万千瓦', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1508', '1', 'SJFDL', '实际发电量', '万千瓦时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1509', '15', 'FJKLYXS', '风机可利用小时', '小时', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1510', '9', 'HYL', '耗油量', '吨', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1511', '9', 'HQL', '耗气量', '千立方米', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1512', '8', 'GLRXL', '锅炉热效率', '', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1513', '8', 'QLJXL', '汽轮机效率', '', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1514', '8', 'QCRXL', '全厂热效率', '', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1515', '8', 'RDB', '热电比', '', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1516', '8', 'YXXS', '运行小时', '', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1517', '8', 'RFHL', '日负荷率', '', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1518', '11', 'FHSW', '防洪水位', '米', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1519', '11', 'KBSGQS', '溃坝事故起数', '起', '企业上报', '60');
INSERT INTO `zhny_dict` VALUES ('1520', '7', 'KCSW', '库存水位', '米', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1521', '7', 'RKSL', '入库水量', '亿立方米', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1522', '7', 'CKSL', '出库水量', '亿立方米', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1523', '7', 'KCSL', '库存水量', '亿立方米', '自动采集', '60');
INSERT INTO `zhny_dict` VALUES ('1524', '7', 'FDHSL', '发电耗水量', '亿立方米', '自动采集', '60');
