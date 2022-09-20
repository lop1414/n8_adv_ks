ALTER TABLE `n8_adv_ks`.`ks_unit_extends`
    ADD COLUMN `convert_callback_strategy_group_id` int(11) NOT NULL COMMENT '回传策略组id' AFTER `convert_callback_strategy_id`;
