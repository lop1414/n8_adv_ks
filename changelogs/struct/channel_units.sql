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

 Date: 02/09/2021 15:24:56
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for channel_units
-- ----------------------------
DROP TABLE IF EXISTS `channel_units`;
CREATE TABLE `channel_units` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `unit_id` bigint(11) NOT NULL COMMENT '广告组id',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
  `platform` varchar(50) NOT NULL DEFAULT '' COMMENT '平台',
  `extends` text COMMENT '扩展字段',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channel_ad` (`channel_id`,`unit_id`,`platform`) USING BTREE,
  KEY `unit_id` (`unit_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道广告组关联表';

SET FOREIGN_KEY_CHECKS = 1;
