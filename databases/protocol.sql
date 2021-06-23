/*
Navicat MySQL Data Transfer

Source Server         : 19.19.19.11
Source Server Version : 50647
Source Host           : 19.19.19.11:3306
Source Database       : alienvault

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-07-09 15:16:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for protocol
-- ----------------------------
DROP TABLE IF EXISTS `protocol`;
CREATE TABLE `protocol` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of protocol
-- ----------------------------
INSERT INTO `protocol` VALUES ('1', '0', 'HOPOPT');
INSERT INTO `protocol` VALUES ('2', '1', 'ICMP');
INSERT INTO `protocol` VALUES ('3', '2', 'IGMP');
INSERT INTO `protocol` VALUES ('4', '3', 'GGP');
INSERT INTO `protocol` VALUES ('5', '4', 'IPV4');
INSERT INTO `protocol` VALUES ('6', '5', 'ST');
INSERT INTO `protocol` VALUES ('7', '6', 'TCP');
INSERT INTO `protocol` VALUES ('8', '7', 'CBT');
INSERT INTO `protocol` VALUES ('9', '8', 'EGP');
INSERT INTO `protocol` VALUES ('10', '9', 'IGP');
INSERT INTO `protocol` VALUES ('11', '10', 'BBN-RCC-MON');
INSERT INTO `protocol` VALUES ('12', '11', 'NVP-||');
INSERT INTO `protocol` VALUES ('13', '12', 'PUP');
INSERT INTO `protocol` VALUES ('14', '13', 'ARGUS');
INSERT INTO `protocol` VALUES ('15', '14', 'EMCON');
INSERT INTO `protocol` VALUES ('16', '15', 'XNET');
INSERT INTO `protocol` VALUES ('17', '16', 'CHAOS');
INSERT INTO `protocol` VALUES ('18', '17', 'UDP');
INSERT INTO `protocol` VALUES ('19', '18', 'MUX');
INSERT INTO `protocol` VALUES ('20', '19', 'DCN-MEAS');
INSERT INTO `protocol` VALUES ('21', '20', 'HMP');
INSERT INTO `protocol` VALUES ('22', '21', 'PRM');
INSERT INTO `protocol` VALUES ('23', '22', 'XNS-IDP');
INSERT INTO `protocol` VALUES ('24', '23', 'TRUNK-1');
INSERT INTO `protocol` VALUES ('25', '24', 'TRUNK-2');
INSERT INTO `protocol` VALUES ('26', '25', 'LEAF-1');
INSERT INTO `protocol` VALUES ('27', '26', 'LEAF-2');
INSERT INTO `protocol` VALUES ('28', '27', 'RDP');
INSERT INTO `protocol` VALUES ('29', '28', 'IRTP');
INSERT INTO `protocol` VALUES ('30', '29', 'ISO-TP4');
INSERT INTO `protocol` VALUES ('31', '30', 'NETBLT');
INSERT INTO `protocol` VALUES ('32', '31', 'MFE-NSP');
INSERT INTO `protocol` VALUES ('33', '32', 'MERIT-INP');
INSERT INTO `protocol` VALUES ('34', '33', 'DCCP');
INSERT INTO `protocol` VALUES ('35', '34', '3PC');
INSERT INTO `protocol` VALUES ('36', '35', 'IDPR');
INSERT INTO `protocol` VALUES ('37', '36', 'XTP');
INSERT INTO `protocol` VALUES ('38', '37', 'DDP');
INSERT INTO `protocol` VALUES ('39', '38', 'IDPR-CMTP');
INSERT INTO `protocol` VALUES ('40', '39', 'TP++');
INSERT INTO `protocol` VALUES ('41', '40', 'IL');
INSERT INTO `protocol` VALUES ('42', '41', 'IPV6');
INSERT INTO `protocol` VALUES ('43', '42', 'SDRP');
INSERT INTO `protocol` VALUES ('44', '43', 'IPV6-Route');
INSERT INTO `protocol` VALUES ('45', '44', 'IPV6-Frag');
INSERT INTO `protocol` VALUES ('46', '45', 'IDRP');
INSERT INTO `protocol` VALUES ('47', '46', 'RSVP');
INSERT INTO `protocol` VALUES ('48', '47', 'GRE');
INSERT INTO `protocol` VALUES ('49', '48', 'DSR');
INSERT INTO `protocol` VALUES ('50', '49', 'BNA');
INSERT INTO `protocol` VALUES ('51', '50', 'ESP');
INSERT INTO `protocol` VALUES ('52', '51', 'AH');
INSERT INTO `protocol` VALUES ('53', '52', 'I-NLSP');
INSERT INTO `protocol` VALUES ('54', '53', 'SWIPE');
INSERT INTO `protocol` VALUES ('55', '54', 'NARP');
INSERT INTO `protocol` VALUES ('56', '55', 'MOBILE');
INSERT INTO `protocol` VALUES ('57', '56', 'TLSP');
INSERT INTO `protocol` VALUES ('58', '57', 'SKIP');
INSERT INTO `protocol` VALUES ('59', '58', 'IPV6-ICMP');
INSERT INTO `protocol` VALUES ('60', '59', 'IPV6-NoNxt');
INSERT INTO `protocol` VALUES ('61', '60', 'IPV6-Opts');
INSERT INTO `protocol` VALUES ('62', '61', null);
INSERT INTO `protocol` VALUES ('63', '62', 'CFTP');
INSERT INTO `protocol` VALUES ('64', '63', null);
INSERT INTO `protocol` VALUES ('65', '64', 'SAT-EXPAK');
INSERT INTO `protocol` VALUES ('66', '65', 'KRYPTOLAN');
INSERT INTO `protocol` VALUES ('67', '66', 'RVD');
INSERT INTO `protocol` VALUES ('68', '67', 'IPPC');
INSERT INTO `protocol` VALUES ('69', '68', null);
INSERT INTO `protocol` VALUES ('70', '69', 'SAT-MON');
INSERT INTO `protocol` VALUES ('71', '70', 'VISA');
INSERT INTO `protocol` VALUES ('72', '71', 'IPCV');
INSERT INTO `protocol` VALUES ('73', '72', 'CPNX');
INSERT INTO `protocol` VALUES ('74', '73', 'CPHB');
INSERT INTO `protocol` VALUES ('75', '74', 'WSN');
INSERT INTO `protocol` VALUES ('76', '75', 'PVP');
INSERT INTO `protocol` VALUES ('77', '76', 'BR-SAT-MON');
INSERT INTO `protocol` VALUES ('78', '77', 'SUN-ND');
INSERT INTO `protocol` VALUES ('79', '78', 'WB-MON');
INSERT INTO `protocol` VALUES ('80', '79', 'WB-EXPAK');
INSERT INTO `protocol` VALUES ('81', '80', 'ISO-IP');
INSERT INTO `protocol` VALUES ('82', '81', 'VMTP');
INSERT INTO `protocol` VALUES ('83', '82', 'SECURE-VMTP');
INSERT INTO `protocol` VALUES ('84', '83', 'VINES');
INSERT INTO `protocol` VALUES ('85', '84', 'TTP');
INSERT INTO `protocol` VALUES ('86', '85', 'NEFNET-IGP');
INSERT INTO `protocol` VALUES ('87', '86', 'DGP');
INSERT INTO `protocol` VALUES ('88', '87', 'TCF');
INSERT INTO `protocol` VALUES ('89', '88', 'EIGRP');
INSERT INTO `protocol` VALUES ('90', '89', 'OSPFIGP');
INSERT INTO `protocol` VALUES ('91', '90', 'Sprite-RPC');
INSERT INTO `protocol` VALUES ('92', '91', 'LARP');
INSERT INTO `protocol` VALUES ('93', '92', 'MTP');
INSERT INTO `protocol` VALUES ('94', '93', 'AX.25');
INSERT INTO `protocol` VALUES ('95', '94', 'IPIP');
INSERT INTO `protocol` VALUES ('96', '95', 'MICP');
INSERT INTO `protocol` VALUES ('97', '96', 'SCC-SP');
INSERT INTO `protocol` VALUES ('98', '97', 'ETHERIP');
INSERT INTO `protocol` VALUES ('99', '98', 'ENCAP');
INSERT INTO `protocol` VALUES ('100', '99', null);
INSERT INTO `protocol` VALUES ('101', '100', 'GMTP');
INSERT INTO `protocol` VALUES ('102', '101', 'IFMP');
INSERT INTO `protocol` VALUES ('103', '102', 'PNNI');
INSERT INTO `protocol` VALUES ('104', '103', 'PIM');
INSERT INTO `protocol` VALUES ('105', '104', 'ARIS');
INSERT INTO `protocol` VALUES ('106', '105', 'SCPS');
INSERT INTO `protocol` VALUES ('107', '106', 'QNX');
INSERT INTO `protocol` VALUES ('108', '107', 'A/N');
INSERT INTO `protocol` VALUES ('109', '108', 'IPComp');
INSERT INTO `protocol` VALUES ('110', '109', 'SNP');
INSERT INTO `protocol` VALUES ('111', '110', 'Compaq-Peer');
INSERT INTO `protocol` VALUES ('112', '111', 'IPX-in-IP');
INSERT INTO `protocol` VALUES ('113', '112', 'VRRP');
INSERT INTO `protocol` VALUES ('114', '113', 'PGM');
INSERT INTO `protocol` VALUES ('115', '114', null);
INSERT INTO `protocol` VALUES ('116', '115', 'L2TP');
INSERT INTO `protocol` VALUES ('117', '116', 'DDX');
INSERT INTO `protocol` VALUES ('118', '117', 'IATP');
INSERT INTO `protocol` VALUES ('119', '118', 'STP');
INSERT INTO `protocol` VALUES ('120', '119', 'SRP');
INSERT INTO `protocol` VALUES ('121', '120', 'UTI');
INSERT INTO `protocol` VALUES ('122', '121', 'SMP');
INSERT INTO `protocol` VALUES ('123', '122', 'SM');
INSERT INTO `protocol` VALUES ('124', '123', 'PTP');
INSERT INTO `protocol` VALUES ('125', '124', 'ISIS over IPV4');
INSERT INTO `protocol` VALUES ('126', '125', 'FITP');
INSERT INTO `protocol` VALUES ('127', '126', 'CRTP');
INSERT INTO `protocol` VALUES ('128', '127', 'CRUDP');
INSERT INTO `protocol` VALUES ('129', '128', 'SSCOPMCE');
INSERT INTO `protocol` VALUES ('130', '129', 'IPLT');
INSERT INTO `protocol` VALUES ('131', '130', 'SPS');
INSERT INTO `protocol` VALUES ('132', '131', 'PIPE');
INSERT INTO `protocol` VALUES ('133', '132', 'SCTP');
INSERT INTO `protocol` VALUES ('134', '133', 'FC');
INSERT INTO `protocol` VALUES ('135', '134', 'PSVP-E2E-IGNORE');
INSERT INTO `protocol` VALUES ('136', '135', 'Mobility Header');
INSERT INTO `protocol` VALUES ('137', '136', 'UDPLite');
INSERT INTO `protocol` VALUES ('138', '137', 'MPLS-in-IP');
INSERT INTO `protocol` VALUES ('139', '138', 'manet');
INSERT INTO `protocol` VALUES ('140', '139', 'HIP');
INSERT INTO `protocol` VALUES ('141', '140', 'WESP');
INSERT INTO `protocol` VALUES ('142', '141', 'ROHC');
INSERT INTO `protocol` VALUES ('143', '142', null);
