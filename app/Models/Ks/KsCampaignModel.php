<?php

namespace App\Models\Ks;

class KsCampaignModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_campaigns';


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
}
