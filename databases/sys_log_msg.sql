/*
 Navicat Premium Data Transfer

 Source Server         : 19.19.19.70
 Source Server Type    : MySQL
 Source Server Version : 50647
 Source Host           : 19.19.19.70:3306
 Source Schema         : alienvault

 Target Server Type    : MySQL
 Target Server Version : 50647
 File Encoding         : 65001

 Date: 04/01/2021 17:43:36
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sys_log_msg
-- ----------------------------
DROP TABLE IF EXISTS `sys_log_msg`;
CREATE TABLE `sys_log_msg`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `log_type_id` int(11) NULL DEFAULT NULL COMMENT '父类id',
  `msg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作信息结果',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 109 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of sys_log_msg
-- ----------------------------
INSERT INTO `sys_log_msg` VALUES (1, 14, '删除资产成功');
INSERT INTO `sys_log_msg` VALUES (2, 14, '删除资产失败');
INSERT INTO `sys_log_msg` VALUES (3, 14, '保存资产成功');
INSERT INTO `sys_log_msg` VALUES (4, 14, '保存资产失败');
INSERT INTO `sys_log_msg` VALUES (5, 14, '导出资产成功');
INSERT INTO `sys_log_msg` VALUES (6, 14, '导入资产成功');
INSERT INTO `sys_log_msg` VALUES (7, 14, '导入资产失败');
INSERT INTO `sys_log_msg` VALUES (8, 15, '保存拓补图成功');
INSERT INTO `sys_log_msg` VALUES (9, 15, '上传拓补图成功');
INSERT INTO `sys_log_msg` VALUES (10, 15, '上传拓补图失败');
INSERT INTO `sys_log_msg` VALUES (11, 15, '删除拓补图成功');
INSERT INTO `sys_log_msg` VALUES (12, 15, '无权删除拓扑图或已被删除');
INSERT INTO `sys_log_msg` VALUES (13, 23, '资产配置成功');
INSERT INTO `sys_log_msg` VALUES (14, 23, '资产配置失败');
INSERT INTO `sys_log_msg` VALUES (15, 23, '网关配置成功');
INSERT INTO `sys_log_msg` VALUES (16, 23, '网关配置失败');
INSERT INTO `sys_log_msg` VALUES (17, 23, '路由配置成功');
INSERT INTO `sys_log_msg` VALUES (18, 23, '路由配置失败');
INSERT INTO `sys_log_msg` VALUES (19, 23, '系统信息配置成功');
INSERT INTO `sys_log_msg` VALUES (20, 23, '系统信息配置失败');
INSERT INTO `sys_log_msg` VALUES (21, 23, '基础配置成功');
INSERT INTO `sys_log_msg` VALUES (22, 23, '基础配置失败');
INSERT INTO `sys_log_msg` VALUES (23, 23, '升级成功');
INSERT INTO `sys_log_msg` VALUES (24, 23, '升级失败');
INSERT INTO `sys_log_msg` VALUES (25, 21, '保存插件成功');
INSERT INTO `sys_log_msg` VALUES (26, 21, '保存插件失败');
INSERT INTO `sys_log_msg` VALUES (27, 21, '删除插件成功');
INSERT INTO `sys_log_msg` VALUES (28, 21, '删除插件失败');
INSERT INTO `sys_log_msg` VALUES (29, 53, '删除情报成功');
INSERT INTO `sys_log_msg` VALUES (30, 53, '删除情报失败');
INSERT INTO `sys_log_msg` VALUES (31, 24, '厂站信息配置成功');
INSERT INTO `sys_log_msg` VALUES (32, 24, '实施信息配置成功');
INSERT INTO `sys_log_msg` VALUES (33, 24, 'vpdn信息配置成功');
INSERT INTO `sys_log_msg` VALUES (34, 25, '成功');
INSERT INTO `sys_log_msg` VALUES (35, 25, '失败');
INSERT INTO `sys_log_msg` VALUES (36, 26, '保存用户成功');
INSERT INTO `sys_log_msg` VALUES (37, 26, '保存用户失败');
INSERT INTO `sys_log_msg` VALUES (40, 26, '删除用户成功');
INSERT INTO `sys_log_msg` VALUES (41, 26, '删除用户失败');
INSERT INTO `sys_log_msg` VALUES (42, 26, '权限开通');
INSERT INTO `sys_log_msg` VALUES (43, 26, '权限关闭');
INSERT INTO `sys_log_msg` VALUES (46, 30, '信息保存成功');
INSERT INTO `sys_log_msg` VALUES (47, 31, '权限分配成功');
INSERT INTO `sys_log_msg` VALUES (48, 31, '权限分配失败');
INSERT INTO `sys_log_msg` VALUES (49, 32, '保存信息成功');
INSERT INTO `sys_log_msg` VALUES (50, 32, '保存信息失败');
INSERT INTO `sys_log_msg` VALUES (51, 23, '系统关机');
INSERT INTO `sys_log_msg` VALUES (52, 23, '系统重启');
INSERT INTO `sys_log_msg` VALUES (53, 23, '系统升级成功');
INSERT INTO `sys_log_msg` VALUES (54, 23, '系统升级失败');
INSERT INTO `sys_log_msg` VALUES (55, 23, '系统信息保存成功');
INSERT INTO `sys_log_msg` VALUES (56, 23, '系统信息保存失败');
INSERT INTO `sys_log_msg` VALUES (57, 23, '系统对时成功');
INSERT INTO `sys_log_msg` VALUES (58, 23, '系统对时失败');
INSERT INTO `sys_log_msg` VALUES (59, 23, '安全认证网关保存成功');
INSERT INTO `sys_log_msg` VALUES (60, 23, '安全认证网关保存失败');
INSERT INTO `sys_log_msg` VALUES (61, 23, '安全日志备份设置成功');
INSERT INTO `sys_log_msg` VALUES (62, 23, '安全日志备份设置失败');
INSERT INTO `sys_log_msg` VALUES (63, 23, '审计日志备份设置成功');
INSERT INTO `sys_log_msg` VALUES (64, 23, '审计日志备份设置失败');
INSERT INTO `sys_log_msg` VALUES (65, 23, '事件转发设置成功');
INSERT INTO `sys_log_msg` VALUES (66, 23, '事件转发设置失败');
INSERT INTO `sys_log_msg` VALUES (67, 23, '告警转发设置成功');
INSERT INTO `sys_log_msg` VALUES (68, 23, '告警转发设置失败');
INSERT INTO `sys_log_msg` VALUES (69, 23, '网口配置成功');
INSERT INTO `sys_log_msg` VALUES (70, 23, '网口配置失败');
INSERT INTO `sys_log_msg` VALUES (71, 23, '路由配置成功');
INSERT INTO `sys_log_msg` VALUES (72, 23, '路由配置失败');
INSERT INTO `sys_log_msg` VALUES (73, 23, '集团管理平台设置成功');
INSERT INTO `sys_log_msg` VALUES (74, 23, '集团管理平台设置失败');
INSERT INTO `sys_log_msg` VALUES (75, 23, '访问平台ip删除成功');
INSERT INTO `sys_log_msg` VALUES (76, 23, '访问平台ip删除失败');
INSERT INTO `sys_log_msg` VALUES (77, 23, '允许访问平台的ip设置成功');
INSERT INTO `sys_log_msg` VALUES (78, 23, '允许访问平台的ip设置失败');
INSERT INTO `sys_log_msg` VALUES (79, 23, '流量配置成功');
INSERT INTO `sys_log_msg` VALUES (80, 23, '流量配置失败');
INSERT INTO `sys_log_msg` VALUES (81, 26, '登录');
INSERT INTO `sys_log_msg` VALUES (82, 26, '登出');
INSERT INTO `sys_log_msg` VALUES (83, 26, '用户登录超时');
INSERT INTO `sys_log_msg` VALUES (84, 21, '子插件保存成功');
INSERT INTO `sys_log_msg` VALUES (85, 21, '子插件保存失败');
INSERT INTO `sys_log_msg` VALUES (86, 21, '子插件删除成功');
INSERT INTO `sys_log_msg` VALUES (87, 21, '子插件删除失败');
INSERT INTO `sys_log_msg` VALUES (90, 20, '保存规则成功');
INSERT INTO `sys_log_msg` VALUES (91, 20, '保存规则失败');
INSERT INTO `sys_log_msg` VALUES (92, 20, '删除规则成功');
INSERT INTO `sys_log_msg` VALUES (93, 20, '删除规则失败');
INSERT INTO `sys_log_msg` VALUES (94, 26, '登陆失败已锁定，稍后重试');
INSERT INTO `sys_log_msg` VALUES (95, 54, '保存网络资产成功');
INSERT INTO `sys_log_msg` VALUES (96, 54, '保存网络资产失败');
INSERT INTO `sys_log_msg` VALUES (97, 54, '删除网络资产成功');
INSERT INTO `sys_log_msg` VALUES (98, 54, '删除网络资产失败');
INSERT INTO `sys_log_msg` VALUES (99, 54, '网络资产配置成功');
INSERT INTO `sys_log_msg` VALUES (100, 54, '网络资产配置失败');
INSERT INTO `sys_log_msg` VALUES (101, 33, '角色删除成功');
INSERT INTO `sys_log_msg` VALUES (102, 33, '角色删除失败');
INSERT INTO `sys_log_msg` VALUES (103, 23, '添加推荐策略成功');
INSERT INTO `sys_log_msg` VALUES (104, 23, '添加推荐策略失败');
INSERT INTO `sys_log_msg` VALUES (105, 55, '保存区域成功');
INSERT INTO `sys_log_msg` VALUES (106, 55, '保存区域失败');
INSERT INTO `sys_log_msg` VALUES (107, 55, '删除区域成功');
INSERT INTO `sys_log_msg` VALUES (108, 55, '删除区域失败');

SET FOREIGN_KEY_CHECKS = 1;
