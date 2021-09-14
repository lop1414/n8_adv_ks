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

 Date: 14/09/2021 15:09:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_program_creative_reports
-- ----------------------------
DROP TABLE IF EXISTS `ks_program_creative_reports`;
CREATE TABLE `ks_program_creative_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stat_datetime` timestamp NULL DEFAULT NULL COMMENT '数据起始时间',
  `account_id` varchar(255) NOT NULL DEFAULT '' COMMENT '账户id',
  `campaign_id` bigint(11) NOT NULL COMMENT '计划id',
  `unit_id` bigint(11) NOT NULL COMMENT '广告组id',
  `creative_id` bigint(22) NOT NULL COMMENT '创意id',
  `charge` int(11) NOT NULL DEFAULT '0' COMMENT '消耗',
  `show` int(11) NOT NULL DEFAULT '0' COMMENT '封面曝光数',
  `photo_click` int(11) NOT NULL DEFAULT '0' COMMENT '封面点击数',
  `aclick` int(11) NOT NULL DEFAULT '0' COMMENT '素材曝光数',
  `bclick` int(11) NOT NULL DEFAULT '0' COMMENT '行为数',
  `extends` text COMMENT '扩展字段',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`stat_datetime`,`creative_id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE,
  KEY `unit_id` (`unit_id`) USING BTREE,
  KEY `creative_id` (`creative_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手程序化创意数据报表';

SET FOREIGN_KEY_CHECKS = 1;
