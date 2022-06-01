<?php

namespace App\Services\Ks\Report;

use App\Common\Enums\MaterialTypeEnums;
use App\Datas\KsMaterialData;
use App\Models\Ks\Report\KsMaterialReportModel;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;

class KsMaterialReportService extends KsReportService
{

    public function setModelClass(): bool
    {
        $this->modelClass = KsMaterialReportModel::class;
        return true;
    }


    public function getContainer(KuaiShou $ksSdk): ApiContainer
    {
        return $ksSdk->materialReport();
    }

    /**
     * api 请求参数
     * @return array
     */
    public function getApiReqParams(): array
    {
        return [
            'temporal_granularity' => 'HOURLY',
            'view_type' => 5
        ];
    }



    /**
     * @param $data
     * @return bool
     * 批量保存
     */
    public function batchSave($data): bool
    {
        // 映射 material_id
        $ksMaterialData = new KsMaterialData();
        foreach ($data as &$item){
            $ksMaterial = $ksMaterialData->save([
                'material_type' => MaterialTypeEnums::VIDEO,
                'file_id'       => $item['photo_id']
            ]);
            $item['material_id'] = $ksMaterial['id'];
        }

        $model = new $this->modelClass();
        $model->chunkInsertOrUpdate($data);
        return true;
    }

}
