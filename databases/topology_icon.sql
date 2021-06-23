/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-10-14 15:24:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for topology_icon
-- ----------------------------
DROP TABLE IF EXISTS `topology_icon`;
CREATE TABLE `topology_icon` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(60) DEFAULT NULL COMMENT 'icon',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `name` varchar(30) DEFAULT NULL COMMENT '类型',
  `width` tinyint(3) DEFAULT NULL COMMENT '宽度',
  `height` tinyint(3) DEFAULT NULL COMMENT '高度',
  `strokeStyle` varchar(30) DEFAULT NULL COMMENT '笔画样式',
  `group` varchar(20) DEFAULT NULL COMMENT '分组',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态-1删除1正常',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='拓扑图icon';

-- ----------------------------
-- Records of topology_icon
-- ----------------------------
INSERT INTO `topology_icon` VALUES ('1', 'upload/topologty/icon/caijiqi.png', '采集器', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:30:25', '2020-03-05 10:01:37');
INSERT INTO `topology_icon` VALUES ('2', 'upload/topologty/icon/fuwuqi.png', '服务器', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:51:56', '2020-03-05 10:01:37');
INSERT INTO `topology_icon` VALUES ('3', 'upload/topologty/icon/luyouqi.png', '路由器', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:52:28', '2020-03-05 10:01:38');
INSERT INTO `topology_icon` VALUES ('4', 'upload/topologty/icon/shebei.png', '设备', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:52:48', '2020-03-05 10:01:43');
INSERT INTO `topology_icon` VALUES ('5', 'upload/topologty/icon/vpn.png', 'VPN', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:53:10', '2020-03-05 10:01:45');
INSERT INTO `topology_icon` VALUES ('6', 'upload/topologty/icon/wangguan.png', '网关', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:53:31', '2020-03-05 10:01:50');
INSERT INTO `topology_icon` VALUES ('7', 'upload/topologty/icon/wangzha.png', '网闸', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:53:56', '2020-03-05 10:01:53');
INSERT INTO `topology_icon` VALUES ('8', 'upload/topologty/icon/zhuji.png', '主机', 'rectangle', '50', '50', '#ffffff00', '网络资产', '1', '2020-03-05 09:54:18', '2020-03-05 10:01:55');