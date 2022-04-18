<?php

namespace App\Models\Ks;

class KsMaterialCreativeModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_material_creatives';


    protected $fillable = [
        'material_id',
        'creative_id',
        'material_type',
        'n8_material_id',
        'signature',
    ];
}
