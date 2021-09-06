<?php

namespace App\Models\Ks;

class KsUnitModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_units';


    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;


    /**
     * @param $value
     * @return float|int
     */
    public function getDayBudgetAttribute($value)
    {

        return $value/1000;
    }


    /**
     * @param $value
     * @return float|int
     */
    public function getBidAttribute($value)
    {

        return $value/1000;
    }

    /**
     * @param $value
     * @return float|int
     */
    public function getCpaBidAttribute($value)
    {

        return $value/1000;
    }



    /**
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getSceneIdAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setSceneIdAttribute($value)
    {
        $this->attributes['scene_id'] = json_encode($value);
    }



    /**
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getDayBudgetScheduleAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setDayBudgetScheduleAttribute($value)
    {
        $this->attributes['day_budget_schedule'] = json_encode($value);
    }





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



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联推广组扩展模型 一对一
     */
    public function ks_unit_extends(){
        return $this->hasOne('App\Models\Ks\KsUnitExtendModel', 'unit_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联渠道模型 一对一
     */
    public function channel_adgroup(){
        return $this->hasOne('App\Models\ChannelUnitModel', 'unit_id', 'id');
    }
}
