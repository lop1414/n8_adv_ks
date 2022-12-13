/*
 Navicat Premium Data Transfer

 Source Server         : mariadb
 Source Server Type    : MariaDB
 Source Server Version : 100336
 Source Host           : localhost:3306
 Source Schema         : n8_adv_ks

 Target Server Type    : MariaDB
 Target Server Version : 100336
 File Encoding         : 65001

 Date: 13/12/2022 19:11:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_account_fund_daily_flows
-- ----------------------------
DROP TABLE IF EXISTS `ks_account_fund_daily_flows`;
CREATE TABLE `ks_account_fund_daily_flows` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` varchar(100) NOT NULL DEFAULT '' COMMENT '账户id',
  `date_time` timestamp NULL DEFAULT NULL COMMENT '数据日期',
  `daily_charge` int(11) NOT NULL DEFAULT 0 COMMENT '总花费',
  `real_charged` int(11) NOT NULL DEFAULT 0 COMMENT '充值花费',
  `contract_rebate_real_charged` int(11) NOT NULL DEFAULT 0 COMMENT '框返花费',
  `direct_rebate_real_charged` int(11) NOT NULL DEFAULT 0 COMMENT '激励花费',
  `daily_transfer_in` int(11) NOT NULL DEFAULT 0 COMMENT '转入',
  `daily_transfer_out` int(11) NOT NULL DEFAULT 0 COMMENT '转出',
  `balance` int(11) NOT NULL DEFAULT 0 COMMENT '日终结余',
  `real_recharged` int(11) NOT NULL DEFAULT 0 COMMENT '充值转入',
  `contract_rebate_real_recharged` int(11) NOT NULL DEFAULT 0 COMMENT '框返转入',
  `direct_rebate_real_recharged` int(11) NOT NULL DEFAULT 0 COMMENT '激励转入',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`date_time`,`account_id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手账户流水';

SET FOREIGN_KEY_CHECKS = 1;
