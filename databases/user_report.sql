/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.70
Source Server Version : 50647
Source Host           : 19.19.19.70:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-12-03 22:51:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_report
-- ----------------------------
DROP TABLE IF EXISTS `user_report`;
CREATE TABLE `user_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) unsigned DEFAULT NULL COMMENT '用户ID',
  `name` char(80) DEFAULT NULL COMMENT '名称',
  `content` text COMMENT '内容',
  `is_mine` tinyint(1) DEFAULT NULL COMMENT '报表类型:0:内置|1:用户添加',
  `is_screen` tinyint(1) DEFAULT '0' COMMENT '是否是大屏:0:否|1:是',
  `thumb` text COMMENT '缩略图',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='报表表';

-- ----------------------------
-- Records of user_report
-- ----------------------------
INSERT INTO `user_report` VALUES ('1', '1', '安全态势', '{\"addForm\":{\"title\":\"安全状况统计报表\",\"name\":\"\",\"nowDate\":\"\",\"row\":3,\"col\":3,\"template\":1,\"isWidth\":1,\"timeRange\":[\"2019-11-17 18:19:48\",\"2019-12-17 18:19:48\"],\"dataRange\":[\"c8\",\"f13\",\"f14\",\"f15\",\"f16\",\"f17\",\"f18\",\"f19\",\"f154\",\"f155\",\"f156\",\"f157\",\"f158\",\"f159\",\"f160\",\"f161\",\"f162\",\"f163\",\"f164\",\"f165\",\"f166\",\"f167\",\"c32\",\"f217\",\"f223\",\"f218\",\"f219\"],\"templateName\":\"安全态势\",\"bgColor\":\"rgba(6, 30, 73, 1)\",\"bgImgUrl\":\"./upload/thumb/20200520093016.gif\"},\"layout\":[{\"x\":0,\"y\":0,\"w\":24,\"h\":2,\"i\":\"9445-1576578002185-43686\",\"d\":{\"resize\":\"false\",\"interFace\":\"title\",\"title\":\"安全态势统计大屏\",\"titlePosition\":\"left\",\"type\":\"\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"\"},\"body\":{\"title\":\"\"},\"id\":\"9445-1576578002185-43686\",\"isaggs\":\"false\"},\"moved\":\"false\"},{\"x\":0,\"y\":9,\"w\":7,\"h\":7,\"i\":\"49fa-1586504237953-81575\",\"d\":{\"body\":{\"title\":\"数据接收状态\"},\"type\":\"LINEC\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(247, 247, 247, 1)\"},\"resize\":\"false\",\"id\":\"49fa-1586504237953-81575\",\"interFace\":\"index/leftcenter\",\"isaggs\":\"false\",\"title\":\"数据接收状态\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":5},\"moved\":\"false\"},{\"x\":7,\"y\":2,\"w\":10,\"h\":14,\"i\":\"7dcc-1586504244215-56996\",\"d\":{\"body\":{\"title\":\"\"},\"type\":\"MAP\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"\"},\"resize\":\"false\",\"id\":\"7dcc-1586504244215-56996\",\"interFace\":\"index/center\",\"isaggs\":\"false\",\"title\":\"\",\"refresh\":0},\"moved\":\"false\"},{\"x\":0,\"y\":2,\"w\":7,\"h\":7,\"i\":\"7039-1586504283623-00762\",\"d\":{\"body\":{\"title\":\"采集器状态\"},\"type\":\"SDA\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"color\":[\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\"],\"id\":\"7039-1586504283623-00762\",\"interFace\":\"index/lefttop\",\"isaggs\":\"false\",\"title\":\"采集器状态\",\"titlePosition\":\"center\",\"bgImg\":\"frame\"},\"moved\":\"false\"},{\"x\":0,\"y\":16,\"w\":7,\"h\":8,\"i\":\"8a27-1586504287475-63384\",\"d\":{\"body\":{\"title\":\"外部访问来源分布\"},\"type\":\"PIEC\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(247, 247, 247, 1)\"},\"resize\":\"false\",\"id\":\"8a27-1586504287475-63384\",\"interFace\":\"index/bottom1\",\"isaggs\":\"false\",\"title\":\"外部访问来源分布\",\"bgImg\":\"frame\",\"titlePosition\":\"center\",\"refresh\":1,\"refreshTime\":60},\"moved\":\"false\"},{\"x\":17,\"y\":16,\"w\":7,\"h\":8,\"i\":\"4a1e-1586504308224-25584\",\"d\":{\"body\":{\"title\":\"资产统计\"},\"type\":\"PIEB\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"id\":\"4a1e-1586504308224-25584\",\"interFace\":\"index/bottom3\",\"isaggs\":\"false\",\"title\":\"资产统计\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":300},\"moved\":\"false\"},{\"x\":7,\"y\":16,\"w\":10,\"h\":8,\"i\":\"158b-1586504316416-32075\",\"d\":{\"body\":{\"title\":\"管理平台性能\"},\"type\":\"CPU\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"id\":\"158b-1586504316416-32075\",\"interFace\":\"index/bottom2\",\"isaggs\":\"false\",\"title\":\"管理平台性能\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":5},\"moved\":\"false\"},{\"x\":17,\"y\":2,\"w\":7,\"h\":14,\"i\":\"789c-1586504325359-72393\",\"d\":{\"body\":{\"title\":\"告警事件\"},\"type\":\"NoScrollList\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"color\":[\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\"],\"id\":\"789c-1586504325359-72393\",\"interFace\":\"index/righttop\",\"isaggs\":\"false\",\"title\":\"告警事件\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":60},\"moved\":\"false\"}]}', '0', '1', './upload/thumb/20200520093016.jpeg', '2020-05-20 17:30:16', '2020-12-03 15:44:58');
INSERT INTO `user_report` VALUES ('2', '1', '安全态势旧版九块', '{\"addForm\":{\"title\":\"安全状况统计报表\",\"name\":\"\",\"nowDate\":\"\",\"row\":3,\"col\":3,\"template\":1,\"isWidth\":1,\"timeRange\":[\"2019-11-17 18:19:48\",\"2019-12-17 18:19:48\"],\"dataRange\":[\"c8\",\"f13\",\"f14\",\"f15\",\"f16\",\"f17\",\"f18\",\"f19\",\"f154\",\"f155\",\"f156\",\"f157\",\"f158\",\"f159\",\"f160\",\"f161\",\"f162\",\"f163\",\"f164\",\"f165\",\"f166\",\"f167\",\"c32\",\"f217\",\"f223\",\"f218\",\"f219\"],\"templateName\":\"安全态势\",\"bgColor\":\"rgba(6, 30, 73, 1)\",\"bgImgUrl\":\"./upload/thumb/20200520093016.gif\"},\"layout\":[{\"x\":0,\"y\":0,\"w\":24,\"h\":2,\"i\":\"9445-1576578002185-43686\",\"d\":{\"resize\":\"false\",\"interFace\":\"title\",\"title\":\"安全态势统计大屏\",\"titlePosition\":\"left\",\"type\":\"\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"\"},\"body\":{\"title\":\"\"},\"id\":\"9445-1576578002185-43686\",\"isaggs\":\"false\"},\"moved\":\"false\"},{\"x\":0,\"y\":9,\"w\":7,\"h\":7,\"i\":\"49fa-1586504237953-81575\",\"d\":{\"body\":{\"title\":\"数据接收状态\"},\"type\":\"LINEC\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(247, 247, 247, 1)\"},\"resize\":\"false\",\"id\":\"49fa-1586504237953-81575\",\"interFace\":\"index/leftcenter\",\"isaggs\":\"false\",\"title\":\"数据接收状态\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":5},\"moved\":\"false\"},{\"x\":7,\"y\":2,\"w\":10,\"h\":14,\"i\":\"7dcc-1586504244215-56996\",\"d\":{\"body\":{\"title\":\"\"},\"type\":\"MAP\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"\"},\"resize\":\"false\",\"id\":\"7dcc-1586504244215-56996\",\"interFace\":\"index/center\",\"isaggs\":\"false\",\"title\":\"\",\"refresh\":0},\"moved\":\"false\"},{\"x\":17,\"y\":9,\"w\":7,\"h\":7,\"i\":\"37a9-1586504251151-59644\",\"d\":{\"body\":{\"title\":\"安全事件\"},\"type\":\"LIST\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"color\":[\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\"],\"id\":\"37a9-1586504251151-59644\",\"interFace\":\"index/rightcenter\",\"isaggs\":\"false\",\"title\":\"安全事件\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":60},\"moved\":\"false\"},{\"x\":0,\"y\":2,\"w\":7,\"h\":7,\"i\":\"7039-1586504283623-00762\",\"d\":{\"body\":{\"title\":\"采集器状态\"},\"type\":\"SDA\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"color\":[\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\"],\"id\":\"7039-1586504283623-00762\",\"interFace\":\"index/lefttop\",\"isaggs\":\"false\",\"title\":\"采集器状态\",\"titlePosition\":\"center\",\"bgImg\":\"frame\"},\"moved\":\"false\"},{\"x\":0,\"y\":16,\"w\":7,\"h\":8,\"i\":\"8a27-1586504287475-63384\",\"d\":{\"body\":{\"title\":\"威胁来源\"},\"type\":\"PIEC\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(247, 247, 247, 1)\"},\"resize\":\"false\",\"id\":\"8a27-1586504287475-63384\",\"interFace\":\"index/bottom1\",\"isaggs\":\"false\",\"title\":\"威胁来源分布\",\"bgImg\":\"frame\",\"titlePosition\":\"center\",\"refresh\":1,\"refreshTime\":60},\"moved\":\"false\"},{\"x\":17,\"y\":16,\"w\":7,\"h\":8,\"i\":\"4a1e-1586504308224-25584\",\"d\":{\"body\":{\"title\":\"资产统计\"},\"type\":\"PIEB\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"id\":\"4a1e-1586504308224-25584\",\"interFace\":\"index/bottom3\",\"isaggs\":\"false\",\"title\":\"资产统计\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":300},\"moved\":\"false\"},{\"x\":7,\"y\":16,\"w\":10,\"h\":8,\"i\":\"158b-1586504316416-32075\",\"d\":{\"body\":{\"title\":\"\"},\"type\":\"CPU\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"id\":\"158b-1586504316416-32075\",\"interFace\":\"index/bottom2\",\"isaggs\":\"false\",\"title\":\"资产性能\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":5},\"moved\":\"false\"},{\"x\":17,\"y\":2,\"w\":7,\"h\":7,\"i\":\"789c-1586504325359-72393\",\"d\":{\"body\":{\"title\":\"告警事件\"},\"type\":\"LIST\",\"otherColor\":{\"bgColor\":\"\",\"titColor\":\"rgba(255, 255, 255, 1)\"},\"resize\":\"false\",\"color\":[\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\",\"rgba(255, 255, 255, 1)\"],\"id\":\"789c-1586504325359-72393\",\"interFace\":\"index/righttop\",\"isaggs\":\"false\",\"title\":\"告警事件\",\"titlePosition\":\"center\",\"bgImg\":\"frame\",\"refresh\":1,\"refreshTime\":60},\"moved\":\"false\"}]}', '1', '0', './upload/thumb/20201127012725.jpeg', '2020-11-26 17:27:25', '2020-11-26 17:30:08');