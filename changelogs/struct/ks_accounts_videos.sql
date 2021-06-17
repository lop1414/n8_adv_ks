/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_adv_ks

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-06-17 14:31:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ks_accounts_videos
-- ----------------------------
DROP TABLE IF EXISTS `ks_accounts_videos`;
CREATE TABLE `ks_accounts_videos` (
  `account_id` varchar(128) NOT NULL DEFAULT '' COMMENT '账户id',
  `video_id` varchar(128) NOT NULL DEFAULT '' COMMENT '视频id',
  UNIQUE KEY `account_video` (`account_id`,`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手账户-视频关联表';
