/*
 Navicat Premium Data Transfer

 Source Server         : 19.19.19.72
 Source Server Type    : MySQL
 Source Server Version : 50647
 Source Host           : 19.19.19.72:3306
 Source Schema         : alienvault

 Target Server Type    : MySQL
 Target Server Version : 50647
 File Encoding         : 65001

 Date: 19/09/2020 10:41:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for host_ip
-- ----------------------------
DROP TABLE IF EXISTS `host_ip`;
CREATE TABLE `host_ip`  (
  `host_id` binary(16) NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `mac` binary(32) NULL DEFAULT NULL,
  `interface` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`host_id`, `ip`) USING BTREE,
  INDEX `ip_index`(`ip`) USING BTREE,
  INDEX `mac_index`(`mac`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Triggers structure for table host_ip
-- ----------------------------
DROP TRIGGER IF EXISTS `host_ip_INSERT`;
delimiter ;;
CREATE TRIGGER `host_ip_INSERT` AFTER INSERT ON `host_ip` FOR EACH ROW BEGIN
    IF @disable_host_update IS NULL THEN
        UPDATE host SET updated=utc_timestamp() WHERE id=NEW.host_id;
    END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table host_ip
-- ----------------------------
DROP TRIGGER IF EXISTS `host_ip_UPDATE`;
delimiter ;;
CREATE TRIGGER `host_ip_UPDATE` AFTER UPDATE ON `host_ip` FOR EACH ROW BEGIN
    IF @disable_host_update IS NULL THEN
        UPDATE host SET updated=utc_timestamp() WHERE id=NEW.host_id;
    END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table host_ip
-- ----------------------------
DROP TRIGGER IF EXISTS `host_ip_DELETE`;
delimiter ;;
CREATE TRIGGER `host_ip_DELETE` AFTER DELETE ON `host_ip` FOR EACH ROW BEGIN
    IF @disable_host_update IS NULL THEN
        UPDATE host SET updated=utc_timestamp() WHERE id=OLD.host_id;
    END IF;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
