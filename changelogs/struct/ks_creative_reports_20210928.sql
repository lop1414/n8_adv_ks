ALTER TABLE `n8_adv_ks`.`ks_creative_reports`
ADD COLUMN `event_new_user_pay` integer(11) NOT NULL DEFAULT 0 COMMENT '新增付费人数' AFTER `bclick`;
