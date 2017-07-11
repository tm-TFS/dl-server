/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : dailian

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-07-11 23:15:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dl_customer
-- ----------------------------
DROP TABLE IF EXISTS `dl_customer`;
CREATE TABLE `dl_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerName` varchar(255) DEFAULT NULL,
  `walletId` int(12) NOT NULL,
  `status` int(2) DEFAULT NULL COMMENT '用户状态 0-禁用 1-启用',
  `vatar` varchar(255) DEFAULT NULL COMMENT '用户微信头像',
  `nickName` varchar(255) DEFAULT NULL COMMENT '昵称',
  `mobile` int(13) DEFAULT NULL COMMENT '手机号码',
  `lastLoginTime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `createUser` varchar(20) DEFAULT NULL,
  `createUserId` int(11) DEFAULT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_customer
-- ----------------------------
INSERT INTO `dl_customer` VALUES ('1', 'bob', '0', '1', '123', 'die', '1888888888', null, null, null, '2017-07-09 16:53:07', '2017-07-09 21:56:24');

-- ----------------------------
-- Table structure for dl_games
-- ----------------------------
DROP TABLE IF EXISTS `dl_games`;
CREATE TABLE `dl_games` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `createUserId` int(12) DEFAULT NULL,
  `createUser` varchar(20) DEFAULT NULL,
  `updateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_games
-- ----------------------------
INSERT INTO `dl_games` VALUES ('1', '王者荣耀', 'wz', '2017-07-09 16:35:22', null, null, '2017-07-09 16:54:01');
INSERT INTO `dl_games` VALUES ('2', '英雄联盟', 'yx', '2017-07-09 16:59:59', null, null, '2017-07-09 16:59:59');

-- ----------------------------
-- Table structure for dl_orders
-- ----------------------------
DROP TABLE IF EXISTS `dl_orders`;
CREATE TABLE `dl_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `useId` int(11) DEFAULT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createUserId` int(12) DEFAULT NULL,
  `createUser` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`useId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_orders
-- ----------------------------

-- ----------------------------
-- Table structure for dl_rate
-- ----------------------------
DROP TABLE IF EXISTS `dl_rate`;
CREATE TABLE `dl_rate` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `customerId` int(12) DEFAULT NULL,
  `serverId` int(12) DEFAULT NULL COMMENT '所在游戏区服',
  `gameId` int(12) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT '订单任务',
  `price` decimal(10,2) DEFAULT NULL,
  `timeLimit` int(12) DEFAULT NULL COMMENT '时限',
  `saveDeposit` int(12) DEFAULT NULL COMMENT '保证金',
  `efficiencyDeposit` int(12) DEFAULT NULL COMMENT '效率保证金',
  `account` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `publishType` int(2) DEFAULT NULL COMMENT '任务频道 1-优质 2-公共',
  `require` varchar(255) DEFAULT NULL COMMENT '代练要求',
  `other` varchar(255) DEFAULT NULL,
  `codeMobile` varchar(255) DEFAULT NULL COMMENT '验证码 手机',
  `contactMobile` varchar(255) DEFAULT NULL,
  `wechatNum` varchar(30) DEFAULT NULL COMMENT '微信号',
  `customerName` varchar(255) DEFAULT NULL,
  `serverName` varchar(255) DEFAULT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createUserId` int(12) DEFAULT NULL,
  `createUser` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerId` (`customerId`) USING BTREE,
  KEY `serverId` (`serverId`) USING BTREE,
  KEY `gameId` (`gameId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_rate
-- ----------------------------
INSERT INTO `dl_rate` VALUES ('1', '1', '5', '1', '黑曜1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', '王者荣耀/苹果QQ/默认服', '2017-07-09 22:00:50', '2017-07-10 22:52:16', '1', 'die');
INSERT INTO `dl_rate` VALUES ('2', '1', '6', '1', '青铜1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', '王者荣耀/苹果微信/微信1区', '2017-07-09 22:00:50', '2017-07-10 22:52:41', '1', 'die');
INSERT INTO `dl_rate` VALUES ('3', '1', '7', '1', '青铜1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:31', '1', 'die');
INSERT INTO `dl_rate` VALUES ('4', '1', '8', '1', '青铜2-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:31', '1', 'die');
INSERT INTO `dl_rate` VALUES ('5', '1', '5', '1', '青铜3-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:31', '1', 'die');
INSERT INTO `dl_rate` VALUES ('6', '1', '6', '1', '青铜4-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:32', '1', 'die');
INSERT INTO `dl_rate` VALUES ('7', '1', '5', '1', '白银1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:32', '1', 'die');
INSERT INTO `dl_rate` VALUES ('8', '1', '5', '1', '青铜2-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:33', '1', 'die');
INSERT INTO `dl_rate` VALUES ('9', '1', '6', '1', '青铜3-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:33', '1', 'die');
INSERT INTO `dl_rate` VALUES ('10', '1', '5', '1', '黄金1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:34', '1', 'die');
INSERT INTO `dl_rate` VALUES ('11', '1', '7', '1', '钻石1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:34', '1', 'die');
INSERT INTO `dl_rate` VALUES ('12', '1', '5', '1', '白银1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:35', '1', 'die');
INSERT INTO `dl_rate` VALUES ('13', '1', '7', '1', '青铜1-王者3星 符文150', '250.00', '24', '150', '150', '18888888888', '123123', '呵呵哒', '2', '微信区 态度好一些，接到单子自己联系客户。', null, '18888888888', '18888888888', 'wx_123123', 'bob', null, '2017-07-09 22:00:50', '2017-07-10 22:51:37', '1', 'die');

-- ----------------------------
-- Table structure for dl_servers
-- ----------------------------
DROP TABLE IF EXISTS `dl_servers`;
CREATE TABLE `dl_servers` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `pid` int(12) DEFAULT '0' COMMENT '上级id 一层为0',
  `name` varchar(255) DEFAULT NULL COMMENT '服务器名',
  `code` varchar(20) DEFAULT NULL COMMENT '服务器代码 例如 wx-001 qq-002',
  `gameId` int(12) NOT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createUserId` int(12) DEFAULT NULL,
  `createUser` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gameId` (`gameId`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_servers
-- ----------------------------
INSERT INTO `dl_servers` VALUES ('1', '0', '安卓QQ', '01', '1', '2017-07-09 21:45:05', '2017-07-11 20:48:57', null, null);
INSERT INTO `dl_servers` VALUES ('2', '0', '安卓微信', '02', '1', '2017-07-09 21:45:29', '2017-07-11 20:48:58', null, null);
INSERT INTO `dl_servers` VALUES ('3', '0', '苹果QQ', '03', '1', '2017-07-09 21:45:47', '2017-07-11 20:48:58', null, null);
INSERT INTO `dl_servers` VALUES ('4', '0', '苹果微信', '04', '1', '2017-07-09 21:46:08', '2017-07-11 20:48:59', null, null);
INSERT INTO `dl_servers` VALUES ('5', '1', '手Q1区', '0101', '1', '2017-07-09 21:47:49', '2017-07-11 20:49:02', null, null);
INSERT INTO `dl_servers` VALUES ('6', '1', '手Q2区', '0102', '1', '2017-07-09 21:48:09', '2017-07-11 20:49:03', null, null);
INSERT INTO `dl_servers` VALUES ('7', '2', '微信1区', '0201', '1', '2017-07-09 21:48:26', '2017-07-11 20:49:04', null, null);
INSERT INTO `dl_servers` VALUES ('8', '2', '微信2区', '0202', '1', '2017-07-09 21:48:35', '2017-07-11 20:49:05', null, null);
INSERT INTO `dl_servers` VALUES ('9', '2', '微信3区', '0203', '1', '2017-07-09 21:48:44', '2017-07-11 20:49:06', null, null);
INSERT INTO `dl_servers` VALUES ('10', '3', '手Q1区', '0301', '1', '2017-07-09 21:47:49', '2017-07-11 20:49:07', null, null);
INSERT INTO `dl_servers` VALUES ('11', '3', '手Q2区', '0302', '1', '2017-07-09 21:49:28', '2017-07-11 20:49:08', null, null);
INSERT INTO `dl_servers` VALUES ('12', '4', '微信一区', '0401', '1', '2017-07-09 21:49:49', '2017-07-11 20:49:10', null, null);

-- ----------------------------
-- Table structure for dl_user
-- ----------------------------
DROP TABLE IF EXISTS `dl_user`;
CREATE TABLE `dl_user` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `password` varchar(50) DEFAULT NULL,
  `userName` varchar(20) DEFAULT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `createUserId` int(12) DEFAULT NULL,
  `createUser` varchar(20) DEFAULT NULL,
  `updatetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_user
-- ----------------------------
INSERT INTO `dl_user` VALUES ('1', '4297f44b13955235245b2497399d7a93', 'admin', '2017-07-09 16:46:44', null, null, '2017-07-09 17:07:09');
INSERT INTO `dl_user` VALUES ('2', null, 'die', '2017-07-09 17:00:40', '1', 'admin', '2017-07-09 17:00:40');

-- ----------------------------
-- Table structure for dl_wallet
-- ----------------------------
DROP TABLE IF EXISTS `dl_wallet`;
CREATE TABLE `dl_wallet` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `balanceAmount` decimal(10,0) unsigned DEFAULT '0' COMMENT '钱包余额',
  `frozenAmount` decimal(10,0) unsigned DEFAULT '0',
  `enableAmount` decimal(10,0) unsigned DEFAULT '0' COMMENT '可用资金',
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `createUser` varchar(20) DEFAULT NULL,
  `updateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of dl_wallet
-- ----------------------------
INSERT INTO `dl_wallet` VALUES ('1', '0', '0', '0', '2017-07-11 12:12:22', null, '2017-07-11 20:21:21');
INSERT INTO `dl_wallet` VALUES ('2', '0', '0', '0', '2017-07-11 13:12:54', null, '2017-07-11 20:21:21');

-- ----------------------------
-- Table structure for dl_walletbilldetail
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
  `createUser` varchar(20) DEFAULT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `walletFromId` (`walletFromId`) USING BTREE,
  KEY `walletToId` (`walletToId`) USING BTREE,
  KEY `tradeOrderId` (`tradeOrderId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dl_walletbilldetail
-- ----------------------------
