/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机9.7.2
 Source Server Type    : MySQL
 Source Server Version : 50732
 Source Host           : localhost:3306
 Source Schema         : n8_adv_ks

 Target Server Type    : MySQL
 Target Server Version : 50732
 File Encoding         : 65001

 Date: 18/04/2022 10:09:24
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_material_creatives
-- ----------------------------
DROP TABLE IF EXISTS `ks_material_creatives`;
CREATE TABLE `ks_material_creatives` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `material_id` varchar(255) NOT NULL DEFAULT '' COMMENT '素材id',
  `creative_id` varchar(255) NOT NULL DEFAULT '' COMMENT '创意id',
  `material_type` varchar(50) NOT NULL DEFAULT '' COMMENT '素材类型',
  `n8_material_id` int(11) NOT NULL DEFAULT '0' COMMENT 'n8素材id',
  `signature` varchar(128) NOT NULL DEFAULT '' COMMENT '签名',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`material_id`,`creative_id`) USING BTREE,
  KEY `creative_id` (`creative_id`) USING BTREE,
  KEY `n8_material_id` (`n8_material_id`) USING BTREE,
  KEY `signature` (`signature`) USING BTREE,
  KEY `updated_at` (`updated_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手素材-创意关联表表';

SET FOREIGN_KEY_CHECKS = 1;
