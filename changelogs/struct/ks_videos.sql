/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_adv_ks

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-06-17 14:31:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ks_videos
-- ----------------------------
DROP TABLE IF EXISTS `ks_videos`;
CREATE TABLE `ks_videos` (
  `id` varchar(128) NOT NULL DEFAULT '' COMMENT '视频id',
  `width` int(11) NOT NULL DEFAULT '0' COMMENT '视频宽度',
  `height` int(11) NOT NULL DEFAULT '0' COMMENT '视频高度',
  `url` varchar(512) NOT NULL DEFAULT '' COMMENT '视频预览链接',
  `cover_url` varchar(512) NOT NULL DEFAULT '' COMMENT '视频首帧图片链接',
  `signature` varchar(64) NOT NULL DEFAULT '' COMMENT '视频签名',
  `upload_time` timestamp NULL DEFAULT NULL COMMENT '上传时间',
  `photo_name` varchar(255) NOT NULL DEFAULT '' COMMENT '视频名称',
  `duration` float NOT NULL DEFAULT '0' COMMENT '视频时长',
  `source` varchar(50) NOT NULL DEFAULT '' COMMENT '素材来源',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `signature` (`signature`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快手视频表';
