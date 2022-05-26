<?php

namespace App\Services\Ks;

use App\Common\Enums\MaterialTypeEnums;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\KsMaterialData;
use App\Models\Ks\KsCreativeModel;
use App\Models\Ks\KsMaterialCreativeModel;
use App\Models\Ks\KsVideoModel;
use App\Sdks\KuaiShou\KuaiShou;
use App\Services\KuaiShouService;

class KsCreativeService extends BaseService
{

    public function sync(array $option = []): bool
    {

        $param = [];
        if(!empty($option['date'])){
            $param['start_date'] = Functions::getDate($option['date']);
            $param['end_date'] = Functions::getDate($option['date']);
        }

        $accountGroup = KuaiShouService::getAccountGroupByToken($option['account_ids'] ?? []);
        $t = microtime(1);

        foreach($accountGroup as $token => $accountList){
            $ksSdk = KuaiShou::init($token);

            $accountChunk = array_chunk($accountList,5);
            foreach ($accountChunk as $accounts){
                $accountIds = array_column($accounts,'account_id');
                $creatives = KuaiShouService::multiGet($ksSdk->creative(),$accountIds,$param);
                foreach($creatives as $creative) {
                    $this->save($creative);
                }
            }
        }

        $t = microtime(1) - $t;
        var_dump($t);
        return true;
    }


    /**
     * @param $creative
     * @return bool
     * 保存
     */
    public function save($creative){
        $ksCreativeModel = new KsCreativeModel();
        $ksCreative = $ksCreativeModel->where('id', $creative['creative_id'])->first();

        if(empty($ksCreative)){
            $ksCreative = new KsCreativeModel();
        }

        $ksCreative->id = $creative['creative_id'];
        $ksCreative->account_id = $creative['account_id'];
        $ksCreative->unit_id = $creative['unit_id'];
        $ksCreative->name = $creative['creative_name'];
        $ksCreative->creative_material_type = $creative['creative_material_type'];
        $ksCreative->photo_id = $creative['photo_id'];
        $ksCreative->status = $creative['status'];
        $ksCreative->put_status = $creative['put_status'];
        $ksCreative->create_channel = $creative['create_channel'];
        $ksCreative->create_time = $creative['create_time'];
        $ksCreative->update_time = $creative['update_time'];
        $ksCreative->extends = $creative;
        $ret = $ksCreative->save();

        if($ret){
            // 添加关联关系
            (new KsVideoService())->relationAccount($ksCreative['account_id'],$ksCreative['photo_id']);
        }

        return $ret;
    }


    // 创意素材分析
    public function creativeMaterial($date){
        $model = new KsCreativeModel();
        $ksMaterialData = new KsMaterialData();
        $ksVideoModel = new KsVideoModel();
        $lastId = 0;
        do{
            $list = $model
                ->where('id','>',$lastId)
                ->whereBetween('create_time', ["{$date} 00:00:00", "{$date} 23:59:59"])
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


                $tmpModel = new KsMaterialCreativeModel();
                $materialCreative = $tmpModel
                    ->where('material_id',$ksMaterial->id)
                    ->where('creative_id',$item->id)
                    ->first();
                if(!empty($materialCreative)){
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
                $n8MaterialId =!empty($video) ? $video->id : 0;


                $materialCreative = $tmpModel;
                $materialCreative->material_id = $ksMaterial->id;
                $materialCreative->creative_id = $item->id;
                $materialCreative->material_type = $ksMaterial->material_type;
                $materialCreative->n8_material_id = $n8MaterialId;
                $materialCreative->signature = $ksVideo->signature;
                $materialCreative->save();
            }


        }while(!$list->isEmpty());
    }
}
