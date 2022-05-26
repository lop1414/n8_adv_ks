<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Models\Ks\KsProgramCreativeModel;
use App\Sdks\KuaiShou\KuaiShou;
use App\Services\KuaiShouService;

class KsProgramCreativeService extends BaseService
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

        $pageSize = 100;

        foreach($accountGroup as $token => $accountList){

            $ksSdk = KuaiShou::init($token);

            $accountChunk = array_chunk($accountList,5);
            foreach ($accountChunk as $accounts){
                $accountIds = array_column($accounts,'account_id');
                $creatives = KuaiShouService::multiGet($ksSdk->programCreative(),$accountIds,$param,1,$pageSize);
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
    public function save($creative): bool
    {
        $ksProgramCreativeModel = new KsProgramCreativeModel();
        $ksProgramCreative = $ksProgramCreativeModel->where('id', $creative['unit_id'])->first();

        if(empty($ksProgramCreative)){
            $ksProgramCreative = new KsProgramCreativeModel();
        }

        $ksProgramCreative->id = $creative['unit_id'];
        $ksProgramCreative->account_id = $creative['account_id'];
        $ksProgramCreative->name = $creative['package_name'];
        $ksProgramCreative->put_status = $creative['put_status'];
        $ksProgramCreative->view_status = $creative['view_status'];
        $ksProgramCreative->create_time = $creative['create_time'];
        $ksProgramCreative->update_time = $creative['update_time'];
        $ksProgramCreative->extends = $creative;
        $ret = $ksProgramCreative->save();

        if($ret){
            // 添加关联关系
            $ksVideoService = new KsVideoService();

            if(!empty($creative['horizontal_photo_ids'])){
                foreach ($creative['horizontal_photo_ids'] as $photoId){
                    $ksVideoService->relationAccount($ksProgramCreative['account_id'],$photoId);
                }
            }
            if(!empty($creative['vertical_photo_ids'])){
                foreach ($creative['vertical_photo_ids'] as $photoId){
                    $ksVideoService->relationAccount($ksProgramCreative['account_id'],$photoId);
                }
            }
        }

        return $ret;
    }
}
