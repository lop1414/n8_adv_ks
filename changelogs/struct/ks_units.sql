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

 Date: 31/08/2021 15:03:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_ad_units
-- ----------------------------
DROP TABLE IF EXISTS `ks_units`;
CREATE TABLE `ks_units` (
  `id` bigint(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `account_id` varchar(50) NOT NULL COMMENT '账户id',
  `campaign_id` bigint(11) NOT NULL COMMENT '计划id',
  `put_status` tinyint(4) NOT NULL COMMENT '投放状态',
  `status` int(11) NOT NULL COMMENT '状态',
  `create_channel` tinyint(4) NOT NULL COMMENT '创建渠道',
  `study_status` tinyint(4) NOT NULL COMMENT '学习期',
  `compensate_status` tinyint(4) NOT NULL COMMENT '赔付状态',
  `bid_type` tinyint(4) DEFAULT NULL COMMENT '出价类型',
  `bid` int(11) DEFAULT '0' COMMENT '出价',
  `cpa_bid` int(11) DEFAULT '0' COMMENT 'OCPC出价',
  `smart_bid` tinyint(4) DEFAULT NULL COMMENT '优先低成本是否自动出价',
  `ocpx_action_type` int(11) DEFAULT NULL COMMENT '优化目标',
  `deep_conversion_type` int(11) DEFAULT NULL COMMENT '深度优化目标',
  `deep_conversion_bid` int(11) DEFAULT '0' COMMENT '深度优化目标出价',
  `day_budget` int(11) DEFAULT '0' COMMENT '单日预算',
  `day_budget_schedule` tinytext NOT NULL COMMENT '分日预算',
  `scene_id` tinytext NOT NULL COMMENT '广告位',
  `roi_ratio` int(11) DEFAULT '0' COMMENT '付费ROI系数',
  `speed` tinyint(4) DEFAULT NULL COMMENT '投放方式',
  `unit_type` tinyint(4) DEFAULT NULL COMMENT '创意制作方式',
  `convert_id` int(11) DEFAULT NULL COMMENT '转化目标id',
  `web_uri_type` tinyint(4) DEFAULT NULL COMMENT 'url类型',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '落地页链接',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `extends` text COMMENT '扩展字段',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE,
  KEY `update_time` (`update_time`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `put_status` (`put_status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手广告组表';

SET FOREIGN_KEY_CHECKS = 1;
