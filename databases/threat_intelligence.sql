/*
Navicat MySQL Data Transfer

Source Server         : 16.16.16.16_3306
Source Server Version : 50647
Source Host           : 16.16.16.16:3306
Source Database       : alienvault_siem

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-15 15:51:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for threat_intelligence
-- ----------------------------
DROP TABLE IF EXISTS `threat_intelligence`;
CREATE TABLE `threat_intelligence` (
  `id` binary(16) NOT NULL COMMENT '内部ID号',
  `event_id` binary(16) NOT NULL,
  `ctx` binary(16) DEFAULT NULL,
  `severity` char(32) DEFAULT NULL COMMENT '严重级别：Critical、High、Medium、Low',
  `family` char(255) DEFAULT NULL COMMENT '威胁类型',
  `detail` varchar(4096) DEFAULT NULL COMMENT '详情',
  `status` tinyint(1) DEFAULT '0' COMMENT '0:没处理;1:已处理;2:已忽略',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of threat_intelligence
-- ----------------------------
INSERT INTO `threat_intelligence` VALUES (0x7D4C11EFBFBD350010EFBFBD65EFBFB1, 0x7D4C11EFBFBD350010EFBFBD65EFBFBD, null, 'medium', '测试', '{\r\n\"id\": \"4676b082dd5c509205d147c8\",\r\n\"ioc_raw\": \"collegefan.collegefan.org\",\r\n\"severity\": \"high\",\r\n\"created_at\": 1474535981,\r\n\"find_at\": 1475065983,\r\n\"update_at\": 1568889312,\r\n\"family\": \"Dynamer木马\",\r\n\"family_desc\": \"这是一个典型的针对Windows核心系统的恶意软件，以完成其任务.Dynamer一旦进入系统内部就执行一系列命令。 它将收集系统设置，Windows版本，网络配置等数据。 收集的数据将被发送到远程攻击者进行分析。\",\r\n\"port\": 8088,\r\n\"related_sample\": [\r\n\"348e5d6f03bf792e239f5596398b63f58d33c360aab07db4cfaf67f5a7925264\"\r\n],\r\n\"related_ip\": [\r\n\"46.38.80.20\"\r\n],\r\n\"related_gangs\": \"\",\r\n\"related_gangs_desc\": \"\",\r\n\"related_events_and_desc\": null,\r\n\"solution\": \"Bladabindi木马将拷贝自身到以下目录，并以与系统进程相近的名称来命名，例如svhost.exe %TEMP% %APPDATA% %USERPROFILE% %ALLUSERSPROFILE% %windir% 木马将以以下方式实现开机自启动： 启动目录：u003cstartup folderu003e <any stringu003e.exe 修改注册表： In subkey: HKLMSoftwareMicrosoftWindows NTCurrentVersionRun 以以下方式确认当前机器已被感染： In subkey: HKCU Sets value: \\\"di\\\" With data: \\\"!\\\" In subkey: HKLMSoftware随机字符串 Sets value: “[kl]\\\" With data: \\\"0\\\" In subkey: HKLMSoftware随机字符串 Sets value: “US” With data: \\\"@\\\" 另外，木马还会将使用net命令将自身加入到防火墙的例外列表。1、建议使用杀毒软件进行查杀。\"\r\n}', '0', '2020-04-28 06:05:33');
INSERT INTO `threat_intelligence` VALUES (0x7D4C11EFBFBD350010EFBFBD65EFBFBD, 0x5DEFBFBD11EA8A990010EFBFBD65EFBF, null, 'high', 'Bladabindi后门', '{\r\n\"id\": \"4676b082dd5c509205d147c8\",\r\n\"ioc_raw\": \"collegefan.collegefan.org\",\r\n\"severity\": \"high\",\r\n\"created_at\": 1474535981,\r\n\"find_at\": 1475065983,\r\n\"update_at\": 1568889312,\r\n\"family\": \"Dynamer木马\",\r\n\"family_desc\": \"这是一个典型的针对Windows核心系统的恶意软件，以完成其任务.Dynamer一旦进入系统内部就执行一系列命令。 它将收集系统设置，Windows版本，网络配置等数据。 收集的数据将被发送到远程攻击者进行分析。\",\r\n\"port\": 8088,\r\n\"related_sample\": [\r\n\"348e5d6f03bf792e239f5596398b63f58d33c360aab07db4cfaf67f5a7925264\"\r\n],\r\n\"related_ip\": [\r\n\"46.38.80.20\"\r\n],\r\n\"related_gangs\": \"\",\r\n\"related_gangs_desc\": \"\",\r\n\"related_events_and_desc\": null,\r\n\"solution\": \"Bladabindi木马将拷贝自身到以下目录，并以与系统进程相近的名称来命名，例如svhost.exe %TEMP% %APPDATA% %USERPROFILE% %ALLUSERSPROFILE% %windir% 木马将以以下方式实现开机自启动： 启动目录：u003cstartup folderu003e <any stringu003e.exe 修改注册表： In subkey: HKLMSoftwareMicrosoftWindows NTCurrentVersionRun 以以下方式确认当前机器已被感染： In subkey: HKCU Sets value: \\\"di\\\" With data: \\\"!\\\" In subkey: HKLMSoftware随机字符串 Sets value: “[kl]\\\" With data: \\\"0\\\" In subkey: HKLMSoftware随机字符串 Sets value: “US” With data: \\\"@\\\" 另外，木马还会将使用net命令将自身加入到防火墙的例外列表。1、建议使用杀毒软件进行查杀。\"\r\n}', '2', '2020-04-13 06:05:33');
