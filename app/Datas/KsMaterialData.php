<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\Ks\KsMaterialModel;

class KsMaterialData extends BaseData
{

    /**
     * @var array
     * 字段
     */
    protected $fields = [];


    /**
     * @var array
     * 唯一键数组
     */
    protected $uniqueKeys = [
        ['material_type','file_id']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(KsMaterialModel::class);
    }



    public function save($data){
        $where = [
            'material_type'    => $data['material_type'],
            'file_id'          => $data['file_id']
        ];
        //清除缓存
        $this->setParams($where)->clear();

        return $this->model->updateOrCreate($where, [
                'material_type'    => $data['material_type'],
                'file_id'          => $data['file_id']
            ]
        );
    }

}
