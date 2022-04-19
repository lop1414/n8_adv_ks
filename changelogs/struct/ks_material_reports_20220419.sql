ALTER TABLE `n8_adv_ks`.`ks_material_reports`
    ADD COLUMN `event_new_user_pay` int(11) NOT NULL COMMENT '新增付费人数' AFTER `bclick`;
