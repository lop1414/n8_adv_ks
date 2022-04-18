<?php

namespace App\Services\Ks\Report;

use App\Common\Enums\MaterialTypeEnums;
use App\Datas\KsMaterialData;
use App\Models\Ks\Report\KsMaterialReportModel;

class KsMaterialReportService extends KsReportService
{

    public function __construct($appId = ''){
        parent::__construct($appId);

        $this->modelClass = KsMaterialReportModel::class;
    }

    /**
     * @param $accounts
     * @param $page
     * @param $pageSize
     * @param array $param
     * @return mixed|void
     * sdk批量获取列表
     */
    public function sdkMultiGetList($accounts, $page, $pageSize, $param = []){
        $param = array_merge($param,[
            'temporal_granularity' => 'HOURLY',
            'view_type' => 5
        ]);
        $param['start_date'] = date('Y-m-d',strtotime($param['start_date_min']));
        $param['end_date'] = date('Y-m-d',strtotime($param['end_date_min']));
        unset($param['start_date_min'],$param['end_date_min']);
        $arr =  $this->sdk->multiGetMaterialReportList($accounts, $page, $pageSize, $param);

        // 映射 material_id
        $ksMaterialData = new KsMaterialData();
        foreach ($arr as &$item){
            if(!isset($item['data']['total_count'])){
                continue;
            }
            foreach ($item['data']['details'] as &$v){
                $ksMaterial = $ksMaterialData->save([
                    'material_type' => MaterialTypeEnums::VIDEO,
                    'file_id'       => $v['photo_id']
                ]);
                $v['material_id'] = $ksMaterial['id'];
            }
        }
        return $arr;
    }
}
