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

 Date: 31/08/2021 10:37:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_campaigns
-- ----------------------------
DROP TABLE IF EXISTS `ks_campaigns`;
CREATE TABLE `ks_campaigns` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '计划id',
  `account_id` bigint(11) NOT NULL COMMENT '账户id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `put_status` tinyint(4) NOT NULL COMMENT '投放状态',
  `status` int(11) NOT NULL COMMENT '状态',
  `day_budget` int(11) NOT NULL COMMENT '单日预算金额',
  `day_budget_schedule` tinytext NOT NULL COMMENT '分日预算',
  `type` tinyint(4) NOT NULL COMMENT '类型',
  `sub_type` tinyint(4) NOT NULL COMMENT '计划子类型',
  `create_channel` tinyint(4) NOT NULL COMMENT '创建渠道',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手计划信息';

SET FOREIGN_KEY_CHECKS = 1;
