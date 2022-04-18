<?php

namespace App\Services\Ks\Report;

use App\Common\Enums\MaterialTypeEnums;
use App\Common\Tools\CustomException;
use App\Datas\KsMaterialData;
use App\Models\Ks\KsMaterialProgramCreativeModel;
use App\Models\Ks\KsVideoModel;
use App\Models\Ks\Report\KsProgramCreativeReportModel;

class KsProgramCreativeReportService extends KsReportService
{
    /**
     * OceanAccountReportService constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);

        $this->modelClass = KsProgramCreativeReportModel::class;
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
        return $this->sdk->multiGetProgramCreativeReportList($accounts, $page, $pageSize, $param);
    }

    /**
     * @param $accountIds
     * @return array|mixed
     * @throws CustomException
     * 按账户消耗执行
     */
    protected function runByAccountCharge($accountIds){
        $ksAccountReportService = new KsAccountReportService();
        $accountReportMap = $ksAccountReportService->getAccountReportByDate()->pluck('charge', 'account_id');

        $creativeReportMap = $this->getAccountReportByDate()->pluck('charge', 'account_id');

        $creativeAccountIds = ['xx'];
        foreach($accountReportMap as $accountId => $charge){
            if(isset($creativeReportMap[$accountId]) && bcsub($creativeReportMap[$accountId] * 1000, $charge * 1000) >= 0){
                continue;
            }
            $creativeAccountIds[] = $accountId;
        }

        return $creativeAccountIds;
    }


    // 创意素材分析
    public function creativeMaterial($date){
        $model = new KsProgramCreativeReportModel();
        $ksMaterialData = new KsMaterialData();
        $ksVideoModel = new KsVideoModel();
        $lastId = 0;
        do{
            $list = $model
                ->where('id','>',$lastId)
                ->whereBetween('stat_datetime', ["{$date} 00:00:00", "{$date} 23:59:59"])
                ->skip(0)
                ->take(1000)
                ->orderBy('id')
                ->get();

            foreach ($list as $item){
                $lastId = $item->id;
                $ksMaterial = $ksMaterialData->save([
                    'material_type'    => MaterialTypeEnums::VIDEO,
                    'file_id'          => $item->photo_id
                ]);

                $tmpModel = new KsMaterialProgramCreativeModel();
                $materialProgramCreative = $tmpModel
                    ->where('material_id',$ksMaterial->id)
                    ->where('creative_id',$item->creative_id)
                    ->first();
                if(!empty($materialProgramCreative)){
                    continue;
                }

                $ksVideo = $ksVideoModel->where('id',$item->photo_id)->first();
                if(empty($ksVideo)){
                    var_dump("找不到视频信息：{$item->photo_id}");
                    continue;
                }

                $videoModel = new \App\Models\Material\VideoModel();
                $video = $videoModel->whereRaw("
                    (signature = '{$ksVideo->signature}' OR source_signature = '{$ksVideo->signature}')
                ")->first();
                $n8MaterialId = !empty($video) ? $video->id : 0;

                $materialProgramCreative = $tmpModel;
                $materialProgramCreative->material_id = $ksMaterial->id;
                $materialProgramCreative->creative_id = $item->creative_id;
                $materialProgramCreative->unit_id = $item->unit_id;
                $materialProgramCreative->material_type = $ksMaterial->material_type;
                $materialProgramCreative->n8_material_id = $n8MaterialId;
                $materialProgramCreative->signature = $ksVideo->signature;
                $materialProgramCreative->save();
            }


        }while(!$list->isEmpty());
    }
}
