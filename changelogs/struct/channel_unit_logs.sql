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

 Date: 02/09/2021 15:25:06
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for channel_unit_logs
-- ----------------------------
DROP TABLE IF EXISTS `channel_unit_logs`;
CREATE TABLE `channel_unit_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_unit_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '渠道广告组id',
  `unit_id` bigint(11) NOT NULL COMMENT '广告组id',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
  `platform` varchar(50) NOT NULL DEFAULT '' COMMENT '平台',
  `extends` text COMMENT '扩展信息',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `channel_id` (`channel_id`) USING BTREE,
  KEY `unit_id` (`unit_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道广告组关联日志表';

SET FOREIGN_KEY_CHECKS = 1;
