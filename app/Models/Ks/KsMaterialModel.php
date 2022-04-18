<?php

namespace App\Models\Ks;

class KsMaterialModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_materials';


    protected $fillable = [
        'material_type',
        'file_id',
    ];
}
