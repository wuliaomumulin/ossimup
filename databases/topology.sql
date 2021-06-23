/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 15:14:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for topology
-- ----------------------------
DROP TABLE IF EXISTS `topology`;
CREATE TABLE `topology` (
  `topology_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topology_name` varchar(60) DEFAULT NULL COMMENT '拓扑名称',
  `topology_remark` varchar(255) NOT NULL COMMENT '拓扑描述',
  `topology_img` varchar(60) DEFAULT NULL COMMENT '拓扑缩略图',
  `topology_content` varchar(60) DEFAULT NULL COMMENT '拓扑内容',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `topology_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '拓扑状态1正常-1删除',
  `update_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`topology_id`) USING BTREE,
  KEY `名称` (`topology_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='网络拓扑图';

-- ----------------------------
-- Records of topology
-- ----------------------------
INSERT INTO `topology` VALUES ('24', '测试', '测试', './upload/topologty/xml/2020-03-25/20200317141044.png', './upload/topologty/xml/2020-03-25/20200316132243.xml', '2', '-1', '2020-04-22 10:54:21', '2020-03-16 10:57:42');
INSERT INTO `topology` VALUES ('25', '333', '333', './upload/topologty/xml/2020-04-17/20200417165616.png', './upload/topologty/xml/2020-04-17/20200417165615.xml', '2', '-1', '2020-04-22 10:54:21', '2020-03-16 11:04:27');
INSERT INTO `topology` VALUES ('28', 'qweqwe', 'qweqwe', './upload/topologty/xml/2020-04-17/20200417170207.png', './upload/topologty/xml/2020-04-17/20200417170207.xml', '2', '-1', '2020-04-22 10:54:23', '2020-04-17 16:32:15');
INSERT INTO `topology` VALUES ('29', 'test', 'test', './upload/topologty/xml/2020-04-17/20200417165109.png', './upload/topologty/xml/2020-04-17/20200417165109.xml', '2', '-1', '2020-04-22 10:54:24', '2020-04-17 16:51:09');
INSERT INTO `topology` VALUES ('30', '3331', '3331', '11111', '11111', '2', '-1', '2020-04-17 16:58:02', '2020-04-17 16:56:36');
INSERT INTO `topology` VALUES ('31', '3331', '3331', null, '11111', '2', '-1', '2020-04-17 16:58:00', '2020-04-17 16:57:01');
INSERT INTO `topology` VALUES ('32', 'ttttttt', 'ttttttt', './upload/topologty/xml/2020-04-17/20200417170413.png', './upload/topologty/xml/2020-04-17/20200417170413.xml', '2', '-1', '2020-04-22 10:54:24', '2020-04-17 17:02:27');
INSERT INTO `topology` VALUES ('33', '22', '33', './upload/topologty/xml/2020-04-22/20200422105437.png', './upload/topologty/xml/2020-04-22/20200422105437.xml', '2', '-1', '2020-04-22 10:56:48', '2020-04-22 10:54:37');
INSERT INTO `topology` VALUES ('34', '22', '33', './upload/topologty/xml/2020-04-22/20200422105654.png', './upload/topologty/xml/2020-04-22/20200422105654.xml', '2', '-1', '2020-04-22 11:01:06', '2020-04-22 10:56:54');
INSERT INTO `topology` VALUES ('35', 'asdf', 'dsf asdf', './upload/topologty/xml/2020-04-22/20200422111924.png', './upload/topologty/xml/2020-04-22/20200422111924.xml', '2', '-1', '2020-04-22 11:19:28', '2020-04-22 11:19:24');
INSERT INTO `topology` VALUES ('36', 'sdf', 'sfd', './upload/topologty/xml/2020-04-22/20200422115111.png', './upload/topologty/xml/2020-04-22/20200422115111.xml', '2', '-1', '2020-04-23 01:12:11', '2020-04-22 11:47:13');
INSERT INTO `topology` VALUES ('37', '111', '1222', null, './upload/topologty/xml/2020-04-22/20200422202858.xml', '2', '-1', '2020-04-23 01:12:10', '2020-04-22 12:16:50');
INSERT INTO `topology` VALUES ('38', 'sf', 'asdf', './upload/topologty/xml/2020-04-23/20200423011222.png', './upload/topologty/xml/2020-04-23/20200423011222.xml', '2', '-1', '2020-04-23 01:12:24', '2020-04-23 01:12:22');
INSERT INTO `topology` VALUES ('39', '啥都', '阿斯蒂芬', './upload/topologty/xml/2020-04-26/20200426025743.png', './upload/topologty/xml/2020-04-26/20200426025743.xml', '2', '-1', '2020-04-25 17:11:36', '2020-04-26 02:57:43');
INSERT INTO `topology` VALUES ('40', '2222', '111', './upload/topologty/xml/2020-05-02/20200502091941.png', './upload/topologty/xml/2020-05-02/20200502091941.xml', '2', '-1', '2020-05-11 13:39:17', '2020-05-02 09:19:41');
INSERT INTO `topology` VALUES ('41', 'www', '大师傅按时', './upload/topologty/xml/2020-05-19/20200519031010.png', './upload/topologty/xml/2020-05-19/20200519031009.xml', null, '1', '2020-05-19 11:10:10', '2020-05-19 11:10:09');
INSERT INTO `topology` VALUES ('42', '撒旦法', '阿斯蒂芬', './upload/topologty/xml/2020-05-19/20200519031040.png', './upload/topologty/xml/2020-05-19/20200519031040.xml', null, '1', '2020-05-19 11:10:40', '2020-05-19 11:10:40');
INSERT INTO `topology` VALUES ('43', '1111', 'undefined', './upload/topologty/xml/2020-05-19/20200519054829.png', './upload/topologty/xml/2020-05-19/20200519054828.xml', null, '1', '2020-05-19 13:48:29', '2020-05-19 13:48:29');
INSERT INTO `topology` VALUES ('44', '1111', '111', './upload/topologty/xml/2020-05-19/20200519054844.png', './upload/topologty/xml/2020-05-19/20200519054844.xml', null, '1', '2020-05-19 13:48:44', '2020-05-19 13:48:44');
INSERT INTO `topology` VALUES ('45', '测试1', 'undefined', './upload/topologty/xml/2020-05-19/20200519055108.png', './upload/topologty/xml/2020-05-19/20200519055108.xml', null, '1', '2020-05-19 13:51:08', '2020-05-19 13:51:08');
INSERT INTO `topology` VALUES ('46', '撒发达', '撒发达梵蒂冈', './upload/topologty/xml/2020-05-19/20200519061605.png', './upload/topologty/xml/2020-05-19/20200519061605.xml', '2', '1', '2020-05-19 14:16:05', '2020-05-19 14:16:05');
INSERT INTO `topology` VALUES ('47', '测试2', 'undefined', './upload/topologty/xml/2020-05-19/20200519064734.png', './upload/topologty/xml/2020-05-19/20200519064734.xml', '2', '1', '2020-05-19 14:47:34', '2020-05-19 14:47:34');
INSERT INTO `topology` VALUES ('48', '哈哈哈', '111', './upload/topologty/xml/2020-05-19/20200519070022.png', './upload/topologty/xml/2020-05-19/20200519070022.xml', '2', '1', '2020-05-19 15:00:22', '2020-05-19 15:00:22');
INSERT INTO `topology` VALUES ('49', '111', 'undefined', './upload/topologty/xml/2020-05-19/20200519092308.png', './upload/topologty/xml/2020-05-19/20200519092308.xml', null, '1', '2020-05-19 17:23:08', '2020-05-19 17:23:08');
INSERT INTO `topology` VALUES ('50', '1233', '33', './upload/topologty/xml/2020-05-19/20200519092325.png', './upload/topologty/xml/2020-05-19/20200519092325.xml', null, '1', '2020-05-19 17:23:25', '2020-05-19 17:23:25');
INSERT INTO `topology` VALUES ('51', '123', '123', './upload/topologty/xml/2020-05-26/20200526153335.png', './upload/topologty/xml/2020-05-26/20200526153335.xml', '2', '1', '2020-05-26 15:33:35', '2020-05-26 15:33:35');
INSERT INTO `topology` VALUES ('52', '123', '123', null, './upload/topologty/xml/2020-05-26/20200526153448.xml', '2', '-1', '2020-05-26 15:45:23', '2020-05-26 15:34:48');
INSERT INTO `topology` VALUES ('53', '123', '123', null, './upload/topologty/xml/2020-05-26/20200526153629.xml', '2', '-1', '2020-05-26 15:48:49', '2020-05-26 15:36:29');
INSERT INTO `topology` VALUES ('54', '321', '321', './upload/topologty/xml/2020-05-26/20200526153906.png', './upload/topologty/xml/2020-05-26/20200526153906.xml', '2', '-1', '2020-05-26 15:46:21', '2020-05-26 15:39:06');
INSERT INTO `topology` VALUES ('55', '31112', '3123123123', null, './upload/topologty/xml/2020-05-26/20200526155342.xml', '2', '-1', '2020-05-26 16:00:09', '2020-05-26 15:53:42');
INSERT INTO `topology` VALUES ('56', '31112', '3123123123', null, './upload/topologty/xml/2020-05-26/20200526155356.xml', '2', '-1', '2020-05-26 16:00:51', '2020-05-26 15:53:56');
INSERT INTO `topology` VALUES ('57', '模板1', '模板1', './upload/topologty/xml/2020-06-26/20200626123605.png', './upload/topologty/xml/2020-06-26/20200626123605.xml', '1', '1', '2020-06-26 12:36:05', '2020-06-26 12:36:05');
INSERT INTO `topology` VALUES ('58', '采集器', '采集器', './upload/topologty/xml/2020-06-26/20200626133139.png', './upload/topologty/xml/2020-06-26/20200626133139.xml', '1', '1', '2020-06-26 13:31:39', '2020-06-26 13:31:39');
INSERT INTO `topology` VALUES ('59', '公司资产', '采集器', './upload/topologty/xml/2020-06-26/20200626133202.png', './upload/topologty/xml/2020-06-26/20200626133202.xml', '1', '1', '2020-06-26 13:32:02', '2020-06-26 13:32:02');
