<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsUnitModel;

class KsUnitService extends KsService
{
    /**
     * constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);
    }



    /**
     * @param $accounts
     * @param $page
     * @param $pageSize
     * @param array $param
     * @return mixed
     * sdk并发获取列表
     */
    public function sdkMultiGetList($accounts, $page, $pageSize, $param = []){
        return $this->sdk->multiGetAdUnitList($accounts, $page, $pageSize, $param);
    }

    /**
     * @param array $option
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($option = []){
        $accountIds = [];
        // 账户id过滤
        if(!empty($option['account_ids'])){
            $accountIds = $option['account_ids'];
        }

        $param = [];
        if(!empty($option['date'])){
            $param['start_date'] = Functions::getDate($option['date']);
            $param['end_date'] = Functions::getDate($option['date']);
        }


        $accountGroup = $this->getAccountGroup($accountIds);

        $t = microtime(1);

        $pageSize = 100;
        foreach($accountGroup as $g){
            $adUnits = $this->multiGetPageList($g, $pageSize, $param);

            Functions::consoleDump('count:'. count($adUnits));


            // 保存
            foreach($adUnits as $adUnit) {
                $this->save($adUnit);
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
    public function save($adUnit){
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
        $ksAdUnit->day_budget_schedule = $adUnit['day_budget_schedule'];
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
