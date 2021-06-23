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

 Date: 04/12/2020 01:25:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for host_sensor_reference
-- ----------------------------
DROP TABLE IF EXISTS `host_sensor_reference`;
CREATE TABLE `host_sensor_reference`  (
  `host_id` binary(16) NOT NULL,
  `sensor_id` binary(16) NOT NULL,
  PRIMARY KEY (`host_id`, `sensor_id`) USING BTREE,
  INDEX `sensor`(`sensor_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Triggers structure for table host_sensor_reference
-- ----------------------------
DROP TRIGGER IF EXISTS `hsr_INS`;
delimiter ;;
CREATE TRIGGER `hsr_INS` AFTER INSERT ON `host_sensor_reference` FOR EACH ROW BEGIN
    IF @disable_calc_perms IS NULL THEN
        CALL update_users_affected_by_sensors(NEW.sensor_id);
    END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table host_sensor_reference
-- ----------------------------
DROP TRIGGER IF EXISTS `hsr_DEL`;
delimiter ;;
CREATE TRIGGER `hsr_DEL` AFTER DELETE ON `host_sensor_reference` FOR EACH ROW BEGIN
    IF @disable_calc_perms IS NULL THEN
        CALL update_users_affected_by_sensors(OLD.sensor_id);
    END IF;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
