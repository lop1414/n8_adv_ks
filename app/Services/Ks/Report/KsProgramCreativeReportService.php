<?php

namespace App\Services\Ks\Report;

use App\Common\Enums\MaterialTypeEnums;
use App\Datas\KsMaterialData;
use App\Models\Ks\KsMaterialProgramCreativeModel;
use App\Models\Ks\KsVideoModel;
use App\Models\Ks\Report\KsProgramCreativeReportModel;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;

class KsProgramCreativeReportService extends KsReportService
{

    public $modelClass = KsProgramCreativeReportModel::class;


    public function getContainer(KuaiShou $ksSdk): ApiContainer
    {
        return $ksSdk->programCreativeReport();
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
                ->groupBy('photo_id')
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
                    var_dump("找不到视频信息：{$item->account_id} - {$item->photo_id}");
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
