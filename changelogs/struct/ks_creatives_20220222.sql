ALTER TABLE `n8_adv_ks`.`ks_creatives`
ADD COLUMN `description` varchar(512) NOT NULL COMMENT '广告语' AFTER `photo_id`;
