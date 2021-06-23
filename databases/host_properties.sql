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

 Date: 04/12/2020 03:02:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for host_properties
-- ----------------------------
DROP TABLE IF EXISTS `host_properties`;
CREATE TABLE `host_properties`  (
  `host_id` binary(16) NOT NULL,
  `property_ref` int(11) NOT NULL,
  `last_modified` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `source_id` int(11) NULL DEFAULT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `extra` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `tzone` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`host_id`, `property_ref`, `value`(255)) USING BTREE,
  INDEX `date`(`last_modified`) USING BTREE,
  INDEX `property_ref`(`property_ref`, `value`(255)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Triggers structure for table host_properties
-- ----------------------------
DROP TRIGGER IF EXISTS `host_properties_INSERT`;
delimiter ;;
CREATE TRIGGER `host_properties_INSERT` AFTER INSERT ON `host_properties` FOR EACH ROW BEGIN
    IF @disable_host_update IS NULL THEN
        UPDATE host SET updated=utc_timestamp() WHERE id=NEW.host_id;
    END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table host_properties
-- ----------------------------
DROP TRIGGER IF EXISTS `host_properties_UPDATE`;
delimiter ;;
CREATE TRIGGER `host_properties_UPDATE` AFTER UPDATE ON `host_properties` FOR EACH ROW BEGIN
    IF @disable_host_update IS NULL THEN
        UPDATE host SET updated=utc_timestamp() WHERE id=NEW.host_id;
    END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table host_properties
-- ----------------------------
DROP TRIGGER IF EXISTS `host_properties_DELETE`;
delimiter ;;
CREATE TRIGGER `host_properties_DELETE` AFTER DELETE ON `host_properties` FOR EACH ROW BEGIN
    IF @disable_host_update IS NULL THEN
        UPDATE host SET updated=utc_timestamp() WHERE id=OLD.host_id;
    END IF;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
