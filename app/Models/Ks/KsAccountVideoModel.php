<?php

namespace App\Models\Ks;

class KsAccountVideoModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_accounts_videos';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;

    /**
     * @var bool
     * 关闭自动更新时间戳
     */
    public $timestamps= false;
}
