ALTER TABLE `n8_adv_ks`.`ks_creative_reports`
MODIFY COLUMN `campaign_id` bigint(11) NOT NULL COMMENT '计划id' AFTER `account_id`,
MODIFY COLUMN `unit_id` bigint(11) NOT NULL COMMENT '广告组id' AFTER `campaign_id`,
MODIFY COLUMN `creative_id` bigint(11) NOT NULL COMMENT '创意id' AFTER `unit_id`;
