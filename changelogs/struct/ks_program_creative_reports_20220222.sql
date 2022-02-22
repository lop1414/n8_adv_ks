ALTER TABLE `n8_adv_ks`.`ks_program_creative_reports`
ADD COLUMN `photo_id` varchar(125) NOT NULL COMMENT '视频 id' AFTER `creative_id`,
ADD COLUMN `description` varchar(512) NOT NULL COMMENT '作品广告语' AFTER `photo_id`,
MODIFY COLUMN `extends` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '扩展字段' AFTER `bclick`;
