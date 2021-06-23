/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 15:20:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for product_type
-- ----------------------------
DROP TABLE IF EXISTS `product_type`;
CREATE TABLE `product_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of product_type
-- ----------------------------
INSERT INTO `product_type` VALUES ('1', '告警');
INSERT INTO `product_type` VALUES ('2', '异常检测');
INSERT INTO `product_type` VALUES ('3', '反病毒程序');
INSERT INTO `product_type` VALUES ('4', '应用');
INSERT INTO `product_type` VALUES ('5', '应用防火墙');
INSERT INTO `product_type` VALUES ('6', '身份验证和DHCP');
INSERT INTO `product_type` VALUES ('7', '保护模式');
INSERT INTO `product_type` VALUES ('8', '数据库');
INSERT INTO `product_type` VALUES ('9', '终端安全');
INSERT INTO `product_type` VALUES ('10', '防火墙');
INSERT INTO `product_type` VALUES ('11', '蜜罐');
INSERT INTO `product_type` VALUES ('12', '基础设施监控');
INSERT INTO `product_type` VALUES ('13', '入侵检测');
INSERT INTO `product_type` VALUES ('14', '入侵防护');
INSERT INTO `product_type` VALUES ('15', '邮件安全');
INSERT INTO `product_type` VALUES ('16', '邮件服务器');
INSERT INTO `product_type` VALUES ('17', '后台管理');
INSERT INTO `product_type` VALUES ('18', '网络访问控制');
INSERT INTO `product_type` VALUES ('19', '网络发现');
INSERT INTO `product_type` VALUES ('20', '操作系统');
INSERT INTO `product_type` VALUES ('21', '其它设备');
INSERT INTO `product_type` VALUES ('22', '代理');
INSERT INTO `product_type` VALUES ('23', '远程应用程序访问');
INSERT INTO `product_type` VALUES ('24', '路由器/交换机');
INSERT INTO `product_type` VALUES ('25', '服务器');
INSERT INTO `product_type` VALUES ('26', '威胁管理');
INSERT INTO `product_type` VALUES ('27', 'VPN');
INSERT INTO `product_type` VALUES ('28', '漏洞扫描器');
INSERT INTO `product_type` VALUES ('29', '网页服务器');
INSERT INTO `product_type` VALUES ('30', '无线安全/管理');
