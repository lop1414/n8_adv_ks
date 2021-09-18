<?php

namespace App\Models\Ks;

class KsUserModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_users';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';


    /**
     * 属性访问器
     * @param $value
     * @return mixed
     */
    public function getExtendAttribute($value){
        return json_decode($value);
    }

    /**
     * 属性修饰器
     * @param $value
     */
    public function setExtendAttribute($value){
        $this->attributes['extend'] = json_encode($value);
    }
}
