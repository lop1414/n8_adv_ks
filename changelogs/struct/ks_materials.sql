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

 Date: 18/04/2022 10:08:57
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_materials
-- ----------------------------
DROP TABLE IF EXISTS `ks_materials`;
CREATE TABLE `ks_materials` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `material_type` varchar(50) NOT NULL DEFAULT '' COMMENT '素材类型',
  `file_id` varchar(255) NOT NULL DEFAULT '' COMMENT '文件id photo_id/pic_id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`material_type`,`file_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手素材表';

SET FOREIGN_KEY_CHECKS = 1;
