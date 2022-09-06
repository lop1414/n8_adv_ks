<?php

namespace App\Enums\Ks;

class KsAdUnitPutStatusEnum
{
    const AD_UNIT_PUT_STATUS_DELIVERY_OK = 1;
    const AD_UNIT_PUT_STATUS_DISABLE = 2;
    const AD_UNIT_PUT_STATUS_DELETE = 3;


    /**
     * @var string
     * 名称
     */
    static public $name = '快手广告组投放状态';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::AD_UNIT_PUT_STATUS_DELIVERY_OK, 'name' => '投放'],
        ['id' => self::AD_UNIT_PUT_STATUS_DISABLE, 'name' => '暂停'],
        ['id' => self::AD_UNIT_PUT_STATUS_DELETE, 'name' => '删除'],
    ];
}
