/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机 192.168.10.10
 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : localhost:3306
 Source Schema         : n8_adv_ks

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 07/09/2021 12:16:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_unit_extends
-- ----------------------------
DROP TABLE IF EXISTS `ks_unit_extends`;
CREATE TABLE `ks_unit_extends` (
  `unit_id` varchar(100) NOT NULL DEFAULT '' COMMENT '广告组id',
  `convert_callback_strategy_id` int(11) NOT NULL DEFAULT '0' COMMENT '回传策略id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`unit_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手广告组扩展表';

SET FOREIGN_KEY_CHECKS = 1;
