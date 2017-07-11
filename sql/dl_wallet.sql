/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : dailain

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-07-11 13:13:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `dl_wallet`
-- ----------------------------
DROP TABLE IF EXISTS `dl_wallet`;
CREATE TABLE `dl_wallet` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `balanceAmount` decimal(10,0) unsigned DEFAULT '0' COMMENT '钱包余额',
  `frozenAmount` decimal(10,0) unsigned DEFAULT '0',
  `enableAmount` decimal(10,0) unsigned DEFAULT '0' COMMENT '可用资金',
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `createUser` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of dl_wallet
-- ----------------------------
INSERT INTO `dl_wallet` VALUES ('1', '0', '0', '0', '2017-07-11 12:12:22', null);
INSERT INTO `dl_wallet` VALUES ('2', '0', '0', '0', '2017-07-11 13:12:54', null);

-- ----------------------------
-- Table structure for `dl_walletbilldetail`
-- ----------------------------
DROP TABLE IF EXISTS `dl_walletbilldetail`;
CREATE TABLE `dl_walletbilldetail` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `walletFromId` int(12) NOT NULL DEFAULT '0' COMMENT '来源钱包编码 0 为第三方',
  `walletToId` int(12) NOT NULL DEFAULT '0' COMMENT '目标钱包编码 0为无',
  `tradeType` int(2) NOT NULL COMMENT '0支付1充值2提现3转账4退款',
  `tradeDescription` varchar(255) CHARACTER SET gbk DEFAULT NULL COMMENT '交易描述',
  `paymentType` int(2) NOT NULL COMMENT '0线下1支付宝2微信3余额支付',
  `tradeOrderId` int(11) NOT NULL COMMENT '支付订单号',
  `tradeNo` varchar(255) CHARACTER SET gbk DEFAULT NULL COMMENT '交易流水号',
  `amount` decimal(10,0) NOT NULL COMMENT '交易金额',
  `isSuccess` int(2) DEFAULT NULL COMMENT '是否成功',
  PRIMARY KEY (`id`),
  KEY `walletFromId` (`walletFromId`) USING BTREE,
  KEY `walletToId` (`walletToId`) USING BTREE,
  KEY `tradeOrderId` (`tradeOrderId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_walletbilldetail
-- ----------------------------
