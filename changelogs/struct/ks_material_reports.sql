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

 Date: 18/04/2022 10:43:47
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_material_reports
-- ----------------------------
DROP TABLE IF EXISTS `ks_material_reports`;
CREATE TABLE `ks_material_reports` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `stat_datetime` timestamp NULL DEFAULT NULL COMMENT '数据起始时间',
  `account_id` varchar(255) NOT NULL DEFAULT '' COMMENT '账户id',
  `material_id` bigint(20) NOT NULL COMMENT '素材id',
  `charge` int(11) NOT NULL DEFAULT '0' COMMENT '消耗',
  `show` int(11) NOT NULL DEFAULT '0' COMMENT '封面曝光数',
  `photo_click` int(11) NOT NULL DEFAULT '0' COMMENT '封面点击数',
  `aclick` int(11) NOT NULL DEFAULT '0' COMMENT '素材曝光数',
  `bclick` int(11) NOT NULL DEFAULT '0' COMMENT '行为数',
  `extends` text COMMENT '扩展字段',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`stat_datetime`,`material_id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手素材报表';

SET FOREIGN_KEY_CHECKS = 1;
