<?php

namespace App\Models\Ks\Report;

use App\Models\Ks\KsModel;

class KsReportModel extends KsModel
{
    /**
     * @var bool
     * 关闭自动更新时间戳
     */
    public $timestamps= false;

    /**
     * 属性访问器
     * @param $value
     * @return mixed
     */
    public function getExtendsAttribute($value){
        return json_decode($value);
    }

    /**
     * 属性修饰器
     * @param $value
     */
    public function setExtendsAttribute($value){
        $this->attributes['extends'] = json_encode($value);
    }



    /**
     * @param $value
     * @return float|int
     * 属性访问器
     */
    public function getChargeAttribute($value)
    {
        return $value / 1000;
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setChargeAttribute($value)
    {
        $this->attributes['charge'] = $value * 1000;
    }
}
