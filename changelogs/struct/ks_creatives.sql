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

 Date: 03/09/2021 17:18:22
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_creatives
-- ----------------------------
DROP TABLE IF EXISTS `ks_creatives`;
CREATE TABLE `ks_creatives` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '创意id',
  `account_id` varchar(50) NOT NULL COMMENT '账户ID',
  `unit_id` bigint(11) NOT NULL COMMENT '广告组ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `creative_material_type` tinyint(4) NOT NULL COMMENT '素材类型',
  `photo_id` varchar(128) NOT NULL DEFAULT '' COMMENT '视频作品ID',
  `status` int(11) NOT NULL COMMENT '状态',
  `put_status` tinyint(4) NOT NULL COMMENT '投放状态',
  `create_channel` tinyint(4) NOT NULL COMMENT '创建渠道',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `extends` text COMMENT '扩展字段',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `unit_id` (`unit_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手自定义创意';

SET FOREIGN_KEY_CHECKS = 1;
