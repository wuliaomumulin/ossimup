/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.70
Source Server Version : 50647
Source Host           : 19.19.19.70:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-12-22 14:17:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for device_types
-- ----------------------------
DROP TABLE IF EXISTS `device_types`;
CREATE TABLE `device_types` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `class` int(11) NOT NULL,
  `enname` varchar(64) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of device_types
-- ----------------------------
INSERT INTO `device_types` VALUES ('1', '服务器', '0', 'Server');
INSERT INTO `device_types` VALUES ('2', '终端设备', '0', 'Endpoint');
INSERT INTO `device_types` VALUES ('3', '手机', '0', 'Mobile');
INSERT INTO `device_types` VALUES ('4', '网络设备', '0', 'Network Device');
INSERT INTO `device_types` VALUES ('5', '外设', '0', 'Peripheral');
INSERT INTO `device_types` VALUES ('6', '工控设备', '0', 'Industrial Device');
INSERT INTO `device_types` VALUES ('7', '安全设备', '0', 'Security Device');
INSERT INTO `device_types` VALUES ('8', '媒体设备', '0', 'Media Device');
INSERT INTO `device_types` VALUES ('9', '通用设备', '0', 'General Purpose');
INSERT INTO `device_types` VALUES ('10', '医疗设备', '0', 'Medical Device');
INSERT INTO `device_types` VALUES ('11', '采集器装置', '0', 'Acquisition Device');
INSERT INTO `device_types` VALUES ('12', '监测设备', '0', 'EnvironmentMonitor');
INSERT INTO `device_types` VALUES ('13', '工业控制系统', '10000', 'ICS');
INSERT INTO `device_types` VALUES ('14', '人机交互系统', '10000', 'HMI');
INSERT INTO `device_types` VALUES ('15', '集散控制系统', '10000', 'DCS');
INSERT INTO `device_types` VALUES ('16', '数据采集监控系统', '10000', 'SCADA');
INSERT INTO `device_types` VALUES ('17', '电力', '10000', 'Power');
INSERT INTO `device_types` VALUES ('18', '铁路', '10000', 'Railway');
INSERT INTO `device_types` VALUES ('19', '航空航天', '10000', 'Aerospace');
INSERT INTO `device_types` VALUES ('20', '通信', '10000', 'Communication');
INSERT INTO `device_types` VALUES ('21', '交通', '10000', 'Traffic');
INSERT INTO `device_types` VALUES ('22', '石油', '10000', 'Petroleum');
INSERT INTO `device_types` VALUES ('23', '石化', '10000', 'Petrochemcial');
INSERT INTO `device_types` VALUES ('24', '核工业', '10000', 'Nuclear');
INSERT INTO `device_types` VALUES ('25', '矿山', '10000', 'Mine');
INSERT INTO `device_types` VALUES ('26', '冶金', '10000', 'Metallurgy');
INSERT INTO `device_types` VALUES ('27', '水利', '10000', 'Water_Project');
INSERT INTO `device_types` VALUES ('28', '烟草', '10000', 'Tobacco');
INSERT INTO `device_types` VALUES ('29', '制造', '10000', 'Manufacture');
INSERT INTO `device_types` VALUES ('30', '邮电通讯', '10000', 'Postal');
INSERT INTO `device_types` VALUES ('31', '环保', '10000', 'Environment');
INSERT INTO `device_types` VALUES ('32', '市政', '10000', 'Municipal');
INSERT INTO `device_types` VALUES ('33', '未知', '10000', 'Unknow');
INSERT INTO `device_types` VALUES ('100', 'HTTP服务器', '1', 'HTTP Server');
INSERT INTO `device_types` VALUES ('101', '邮件服务器', '1', 'Mail Server');
INSERT INTO `device_types` VALUES ('102', '域控服务器', '1', 'Domain Controller');
INSERT INTO `device_types` VALUES ('103', '域名服务器', '1', 'DNS Server');
INSERT INTO `device_types` VALUES ('104', '文件服务器', '1', 'File Server');
INSERT INTO `device_types` VALUES ('105', '代理服务器', '1', 'Proxy Server');
INSERT INTO `device_types` VALUES ('106', '程控交换机', '1', 'PBX');
INSERT INTO `device_types` VALUES ('107', '打印服务器', '1', 'Print Server');
INSERT INTO `device_types` VALUES ('108', '终端服务器', '1', 'Terminal Server');
INSERT INTO `device_types` VALUES ('109', 'VoIP适配器', '1', 'VoIP Adapter');
INSERT INTO `device_types` VALUES ('110', '域控服务器', '1', 'Active Directory Server / Domain Controller');
INSERT INTO `device_types` VALUES ('117', '时间服务器', '1', 'Time Server');
INSERT INTO `device_types` VALUES ('118', '监控工具服务器', '1', 'Monitoring Tools Server (Nagios, Tivoli, usw.)');
INSERT INTO `device_types` VALUES ('119', '数据库服务器', '1', 'Database Server');
INSERT INTO `device_types` VALUES ('120', 'VPN网关', '1', 'VPN Gateway');
INSERT INTO `device_types` VALUES ('121', '工作站', '1', 'Workstation');
INSERT INTO `device_types` VALUES ('122', '通用应用服务器', '1', 'Application Server (Generic)');
INSERT INTO `device_types` VALUES ('123', '虚拟主机', '1', 'Virtual Host');
INSERT INTO `device_types` VALUES ('124', '付款机', '1', 'Payment Server (ACI in particular)');
INSERT INTO `device_types` VALUES ('125', '贩售点终端机', '1', 'Point of Sale Controller');
INSERT INTO `device_types` VALUES ('126', '其它', '1', 'Server (Other)');
INSERT INTO `device_types` VALUES ('127', 'Web服务器', '1', 'Web Server');
INSERT INTO `device_types` VALUES ('128', 'DMZ服务器', '1', 'DMZ Server');
INSERT INTO `device_types` VALUES ('129', '互联网服务器', '1', 'Internal Server');
INSERT INTO `device_types` VALUES ('130', '备份服务器', '1', 'Backup Server');
INSERT INTO `device_types` VALUES ('131', 'DHCP服务器', '1', 'DHCP Server');
INSERT INTO `device_types` VALUES ('150', '工控师站', '1', 'Server');
INSERT INTO `device_types` VALUES ('151', '操作员站', '1', 'Server');
INSERT INTO `device_types` VALUES ('152', '风机监控', '1', 'Server');
INSERT INTO `device_types` VALUES ('153', '工程师站', '1', 'EWS');
INSERT INTO `device_types` VALUES ('154', '数据库', '1', 'DB');
INSERT INTO `device_types` VALUES ('155', '未知服务器', '1', 'unknow ');
INSERT INTO `device_types` VALUES ('200', '便携式电脑', '2', 'Laptop');
INSERT INTO `device_types` VALUES ('201', '其它', '2', 'Endpoint (Other)');
INSERT INTO `device_types` VALUES ('202', '工作站', '2', 'Workstation');
INSERT INTO `device_types` VALUES ('203', '远程终端单元', '2', 'RTU');
INSERT INTO `device_types` VALUES ('301', '移动电话', '3', 'Cell Phone');
INSERT INTO `device_types` VALUES ('302', '手写板', '3', 'Tablet');
INSERT INTO `device_types` VALUES ('304', '掌上电脑', '3', 'PDA');
INSERT INTO `device_types` VALUES ('305', '可视电话', '3', 'VoIP Phone');
INSERT INTO `device_types` VALUES ('401', '路由器', '4', 'Router');
INSERT INTO `device_types` VALUES ('402', '交换机', '4', 'Switch');
INSERT INTO `device_types` VALUES ('403', 'VPN设备', '4', 'VPN device');
INSERT INTO `device_types` VALUES ('404', '无线AP', '4', 'Wireless AP');
INSERT INTO `device_types` VALUES ('405', '网桥', '4', 'Bridge');
INSERT INTO `device_types` VALUES ('406', '宽带路由器', '4', 'Broadband Router');
INSERT INTO `device_types` VALUES ('407', '远程管理', '4', 'Remote Management');
INSERT INTO `device_types` VALUES ('408', '存储', '4', 'Storage');
INSERT INTO `device_types` VALUES ('409', '集线器', '4', 'Hub');
INSERT INTO `device_types` VALUES ('410', '负载均衡', '4', 'Load Balancer');
INSERT INTO `device_types` VALUES ('411', '防火墙', '4', 'Firewall');
INSERT INTO `device_types` VALUES ('412', '工业交换机', '6', 'Switch');
INSERT INTO `device_types` VALUES ('413', '基础设施路由器', '4', 'InfrastructureRouter');
INSERT INTO `device_types` VALUES ('414', '网络存储器', '4', 'NAS');
INSERT INTO `device_types` VALUES ('415', 'Network', '4', 'Network');
INSERT INTO `device_types` VALUES ('416', '网络分析器', '4', 'NetworkAnalyzer');
INSERT INTO `device_types` VALUES ('417', '网络电话', '4', 'VOIP');
INSERT INTO `device_types` VALUES ('418', '无线网络', '4', 'WIFI');
INSERT INTO `device_types` VALUES ('419', '网络摄像机', '4', 'IP_Camera');
INSERT INTO `device_types` VALUES ('420', '数字录像机', '4', 'DVR');
INSERT INTO `device_types` VALUES ('421', '网络视频录像机', '4', 'NVR');
INSERT INTO `device_types` VALUES ('422', '视频管理服务', '4', 'VMS');
INSERT INTO `device_types` VALUES ('423', '网关', '4', 'Gateway');
INSERT INTO `device_types` VALUES ('424', '电梯监控', '4', 'Elevator');
INSERT INTO `device_types` VALUES ('425', '灾害预警', '4', 'Disaster');
INSERT INTO `device_types` VALUES ('426', '虚拟专用网络', '4', 'VPN');
INSERT INTO `device_types` VALUES ('427', '匿名网络', '4', 'TOR');
INSERT INTO `device_types` VALUES ('428', '非常规加密网络通信', '4', 'Encryption');
INSERT INTO `device_types` VALUES ('429', '特殊即时通讯软件', '4', 'IM');
INSERT INTO `device_types` VALUES ('502', '照相机', '5', 'Camera');
INSERT INTO `device_types` VALUES ('503', '终端机', '5', 'Terminal');
INSERT INTO `device_types` VALUES ('504', '不间断电源(UPS)', '5', 'Uninterrupted Power Supply (UPS)');
INSERT INTO `device_types` VALUES ('505', '能量存储单元(PDU)', '5', 'Power Distribution Unit (PDU)');
INSERT INTO `device_types` VALUES ('506', '环境监控', '5', 'Environmental Monitoring');
INSERT INTO `device_types` VALUES ('507', '其它', '5', 'Peripheral (Other)');
INSERT INTO `device_types` VALUES ('508', '智能平台管理界面', '5', 'IPMI');
INSERT INTO `device_types` VALUES ('509', '磁盘阵列', '5', 'RAID');
INSERT INTO `device_types` VALUES ('510', '打印机', '5', 'PhaserPrinter');
INSERT INTO `device_types` VALUES ('511', '打印机', '5', 'LaserPrinter');
INSERT INTO `device_types` VALUES ('512', '喷墨式打印机', '5', 'InkjetPrinter');
INSERT INTO `device_types` VALUES ('513', '智能管理平台接口', '5', 'IPMI');
INSERT INTO `device_types` VALUES ('514', '灯光控制器', '5', 'LightController');
INSERT INTO `device_types` VALUES ('515', '多功能打印机', '5', 'MultifunctionPrinter');
INSERT INTO `device_types` VALUES ('516', '电源分配单元', '5', 'PDU');
INSERT INTO `device_types` VALUES ('517', '太阳能电池', '5', 'SolarPanel');
INSERT INTO `device_types` VALUES ('518', '存储器', '5', 'Storage');
INSERT INTO `device_types` VALUES ('519', '温度监视器', '5', 'TemperatureMonitor');
INSERT INTO `device_types` VALUES ('520', '恒温器', '5', 'Thermostat');
INSERT INTO `device_types` VALUES ('521', 'UPS', '5', 'UPS');
INSERT INTO `device_types` VALUES ('522', 'USB', '5', 'USB');
INSERT INTO `device_types` VALUES ('523', '液体流量控制器', '5', 'Waterflowcontroller');
INSERT INTO `device_types` VALUES ('524', '可编程逻辑控制器', '5', 'PLC');
INSERT INTO `device_types` VALUES ('525', '网络打印机', '5', 'Printer');
INSERT INTO `device_types` VALUES ('601', '控制器', '6', 'PLC');
INSERT INTO `device_types` VALUES ('602', '分布式控制系统', '6', 'DCS');
INSERT INTO `device_types` VALUES ('603', '人机界面', '6', 'HMI');
INSERT INTO `device_types` VALUES ('604', '功耗测试仪', '6', 'PM');
INSERT INTO `device_types` VALUES ('605', '供热系统与空气调节', '6', 'HVAC');
INSERT INTO `device_types` VALUES ('606', '接入控制系统', '6', 'AC');
INSERT INTO `device_types` VALUES ('702', '入侵检测', '7', 'Intrusion Detection System (IDS)');
INSERT INTO `device_types` VALUES ('703', '入侵防御', '7', 'Intrusion Prevention System (IPS)');
INSERT INTO `device_types` VALUES ('704', '正向隔离网闸', '7', 'Forward Isolation Gate');
INSERT INTO `device_types` VALUES ('705', '反向隔离网闸', '7', 'Reverse Isolation Gate');
INSERT INTO `device_types` VALUES ('706', '应用防火墙（WAF）', '7', 'Web Application Firewall');
INSERT INTO `device_types` VALUES ('707', '防火墙', '7', 'Firewall');
INSERT INTO `device_types` VALUES ('708', '漏扫', '7', 'Vulnerability Scanners');
INSERT INTO `device_types` VALUES ('709', '流量清洗', '7', 'DDOS Protection');
INSERT INTO `device_types` VALUES ('710', '防病毒', '7', 'Anti-Virus');
INSERT INTO `device_types` VALUES ('712', '日志审计', '7', 'Log Audit');
INSERT INTO `device_types` VALUES ('713', '主机防护', '7', 'Host Protection');
INSERT INTO `device_types` VALUES ('799', '其它', '5', 'Security (Other)');
INSERT INTO `device_types` VALUES ('800', '通用设备', '9', 'General Pur');
INSERT INTO `device_types` VALUES ('801', '游戏机', '8', 'Game Console');
INSERT INTO `device_types` VALUES ('802', '电视机', '8', 'Television');
INSERT INTO `device_types` VALUES ('803', '机顶盒', '8', 'Set Top Box');
INSERT INTO `device_types` VALUES ('804', '其它', '8', 'IoT Device (Other)');
INSERT INTO `device_types` VALUES ('805', '家用路由器', '8', 'HomeRouter');
INSERT INTO `device_types` VALUES ('806', '数字视频变换盒', '8', 'Set-topBox');
INSERT INTO `device_types` VALUES ('807', '广告大屏', '8', 'Screen');
INSERT INTO `device_types` VALUES ('902', 'dsl调制解调器', '4', 'DSLModem');
INSERT INTO `device_types` VALUES ('903', '其他', '5', 'Cinema');
INSERT INTO `device_types` VALUES ('904', '其他', '5', 'PowerCrontroller');
INSERT INTO `device_types` VALUES ('905', '电缆调制解调器', '4', 'CableModem');
INSERT INTO `device_types` VALUES ('1001', '其它', '10', 'Other');
INSERT INTO `device_types` VALUES ('1101', '区域一-工业安全流量日志分析系统', '11', 'Sensor I');
INSERT INTO `device_types` VALUES ('1102', '区域二-工业安全流量日志分析系统', '11', 'Sensor II');
INSERT INTO `device_types` VALUES ('1103', '区域三-工业安全流量日志分析系统', '11', 'Sensor III');
INSERT INTO `device_types` VALUES ('1105', '工业统一行为管理平台', '11', 'MajorPlat');
INSERT INTO `device_types` VALUES ('1201', '区域一-工控网络安全监测审计系统', '11', 'Shenji I');
INSERT INTO `device_types` VALUES ('1202', '区域二-工控网络安全监测审计系统', '11', 'Shenji II');
INSERT INTO `device_types` VALUES ('1203', '区域三-工控网络安全监测审计系统', '11', 'Shenji III');
INSERT INTO `device_types` VALUES ('1301', '监测控制和数据采集控制器', '12', 'ScadaController');
INSERT INTO `device_types` VALUES ('1302', '监测控制和数据采集网关', '12', 'ScadaGateway');
INSERT INTO `device_types` VALUES ('1303', '监测控制和数据采集分析器', '12', 'ScadaProcessor');
INSERT INTO `device_types` VALUES ('1304', '监测控制和数据采集路由器', '12', 'ScadaRouter');
INSERT INTO `device_types` VALUES ('1305', '监测控制和数据采集前端', '12', 'ScadaFrontend');
INSERT INTO `device_types` VALUES ('1306', '监测控制和数据采集服务器', '12', 'ScadaServer');
INSERT INTO `device_types` VALUES ('1401', '现场管理终端', '2', 'Scenepoint');
INSERT INTO `device_types` VALUES ('1402', '卷烟机工控机', '6', 'JuanYanControl');
INSERT INTO `device_types` VALUES ('1403', '包装机数采工作站', '6', 'BaoSensor');
INSERT INTO `device_types` VALUES ('1404', '综合测试台', '6', 'CompreTestTai');
INSERT INTO `device_types` VALUES ('1405', '大树小包成像检测', '6', 'BigImaging');
INSERT INTO `device_types` VALUES ('1406', '小包成像检测', '6', 'SmallImaging');
