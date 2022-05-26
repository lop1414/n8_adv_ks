<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Models\Ks\KsUnitModel;
use App\Sdks\KuaiShou\KuaiShou;
use App\Services\KuaiShouService;

class KsUnitService extends BaseService
{

    public function sync($option = []): bool
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
                $adUnits = KuaiShouService::multiGet($ksSdk->adUnit(),$accountIds,$param);
                foreach($adUnits as $adUnit) {
                    $this->save($adUnit);
                }
            }
        }

        $t = microtime(1) - $t;
        var_dump($t);

        return true;
    }


    /**
     * @param $adUnit
     * @return bool
     * 保存
     */
    public function save($adUnit): bool
    {
        $ksAdUnitModel = new KsUnitModel();
        $ksAdUnit = $ksAdUnitModel->where('id', $adUnit['unit_id'])->first();

        if(empty($ksAdUnit)){
            $ksAdUnit = new KsUnitModel();
        }

        $ksAdUnit->id = $adUnit['unit_id'];
        $ksAdUnit->name = $adUnit['unit_name'];
        $ksAdUnit->account_id = $adUnit['account_id'];
        $ksAdUnit->campaign_id = $adUnit['campaign_id'];
        $ksAdUnit->put_status = $adUnit['put_status'];
        $ksAdUnit->status = $adUnit['status'];
        $ksAdUnit->create_channel = $adUnit['create_channel'];
        $ksAdUnit->study_status = $adUnit['study_status'];
        $ksAdUnit->compensate_status = $adUnit['compensate_status'];
        $ksAdUnit->bid_type = $adUnit['bid_type'];
        $ksAdUnit->bid = $adUnit['bid'];
        $ksAdUnit->cpa_bid = $adUnit['cpa_bid'];
        $ksAdUnit->smart_bid = $adUnit['smart_bid'];
        $ksAdUnit->ocpx_action_type = $adUnit['ocpx_action_type'];
        $ksAdUnit->deep_conversion_type = $adUnit['deep_conversion_type'];
        $ksAdUnit->deep_conversion_bid = $adUnit['deep_conversion_bid'];
        $ksAdUnit->day_budget = $adUnit['day_budget'];
        $ksAdUnit->day_budget_schedule = $adUnit['day_budget_schedule'] ?? [];
        $ksAdUnit->scene_id = $adUnit['scene_id'];
        $ksAdUnit->roi_ratio = $adUnit['roi_ratio'];
        $ksAdUnit->speed = $adUnit['speed'];
        $ksAdUnit->unit_type = $adUnit['unit_type'];
        $ksAdUnit->convert_id = $adUnit['convert_id'];
        $ksAdUnit->web_uri_type = $adUnit['web_uri_type'];
        $ksAdUnit->url = $adUnit['url'];
        $ksAdUnit->create_time = $adUnit['create_time'];
        $ksAdUnit->update_time = $adUnit['update_time'];
        $ksAdUnit->extends = $adUnit;
        $ret = $ksAdUnit->save();

        return $ret;
    }
}
