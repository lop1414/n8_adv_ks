/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_adv_ks

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-08-24 15:08:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ks_account_reports
-- ----------------------------
DROP TABLE IF EXISTS `ks_account_reports`;
CREATE TABLE `ks_account_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` varchar(255) NOT NULL DEFAULT '' COMMENT '账户id',
  `stat_datetime` timestamp NULL DEFAULT NULL COMMENT '数据起始时间',
  `charge` int(11) NOT NULL DEFAULT '0' COMMENT '花费',
  `show` int(11) NOT NULL DEFAULT '0' COMMENT '封面曝光数',
  `photo_click` int(11) NOT NULL DEFAULT '0' COMMENT '封面点击数',
  `aclick` int(11) NOT NULL DEFAULT '0' COMMENT '素材曝光数',
  `bclick` int(11) NOT NULL DEFAULT '0' COMMENT '行为数',
  `extends` text COMMENT '扩展字段',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`stat_datetime`,`account_id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8 COMMENT='快手广告账户数据报表';
