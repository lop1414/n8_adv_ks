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

 Date: 18/09/2021 10:44:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ks_users
-- ----------------------------
DROP TABLE IF EXISTS `ks_users`;
CREATE TABLE `ks_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `app_id` varchar(50) NOT NULL,
  `access_token` varchar(50) NOT NULL DEFAULT '',
  `refresh_token` varchar(50) NOT NULL DEFAULT '',
  `fail_at` timestamp NULL DEFAULT NULL COMMENT 'token 过期时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `extend` text COMMENT '扩展字段',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fail_at` (`fail_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手用户表';

SET FOREIGN_KEY_CHECKS = 1;
