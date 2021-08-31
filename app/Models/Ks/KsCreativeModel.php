<?php

namespace App\Models\Ks;

class KsCreativeModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_creatives';


    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;




    /**
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getExtendsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setExtendsAttribute($value)
    {
        $this->attributes['extends'] = json_encode($value);
    }
}
