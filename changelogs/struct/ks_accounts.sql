/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_adv_ks

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-06-17 14:30:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ks_accounts
-- ----------------------------
DROP TABLE IF EXISTS `ks_accounts`;
CREATE TABLE `ks_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(50) NOT NULL DEFAULT '' COMMENT '应用id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `company` varchar(100) NOT NULL DEFAULT '' COMMENT '公司',
  `belong_platform` varchar(50) NOT NULL DEFAULT '' COMMENT '归宿平台',
  `account_id` varchar(50) NOT NULL DEFAULT '' COMMENT '广告账户id',
  `user_id` varchar(50) NOT NULL DEFAULT '' COMMENT '快手用户id',
  `access_token` varchar(50) NOT NULL DEFAULT '',
  `refresh_token` varchar(50) NOT NULL DEFAULT '',
  `fail_at` timestamp NULL DEFAULT NULL COMMENT 'token 过期时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `extend` text COMMENT '扩展字段',
  `parent_id` varchar(50) DEFAULT NULL COMMENT '父级id',
  `status` varchar(50) NOT NULL DEFAULT '' COMMENT '状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ad` (`app_id`,`account_id`) USING BTREE,
  KEY `fail_at` (`fail_at`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4322 DEFAULT CHARSET=utf8 COMMENT='快手账户表';
