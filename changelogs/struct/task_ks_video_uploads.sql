/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_adv_ks

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-06-17 14:31:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for task_ks_video_uploads
-- ----------------------------
DROP TABLE IF EXISTS `task_ks_video_uploads`;
CREATE TABLE `task_ks_video_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL DEFAULT '0' COMMENT '父任务id',
  `app_id` varchar(255) NOT NULL DEFAULT '' COMMENT '应用id',
  `account_id` varchar(255) NOT NULL DEFAULT '' COMMENT '账户id',
  `n8_material_video_id` int(11) NOT NULL DEFAULT '0' COMMENT 'n8素材系统视频id',
  `n8_material_video_path` varchar(512) NOT NULL DEFAULT '' COMMENT 'n8素材系统视频地址',
  `n8_material_video_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'n8素材系统视频名称',
  `n8_material_video_signature` varchar(64) NOT NULL DEFAULT '' COMMENT 'n8素材系统视频签名',
  `exec_status` varchar(50) NOT NULL DEFAULT '' COMMENT '执行状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `extends` text COMMENT '扩展字段',
  `fail_data` text COMMENT '失败数据',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`) USING BTREE,
  KEY `task_id` (`task_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8 COMMENT='快手视频上传任务表';
