<?php

namespace App\Enums\Ks;

class KsSyncTypeEnum
{
    const VIDEO = 'VIDEO';

    /**
     * @var string
     * 名称
     */
    static public $name = '快手同步类型';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::VIDEO, 'name' => '视频'],
    ];
}
