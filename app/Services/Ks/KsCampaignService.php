<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsCampaignModel;

class KsCampaignService extends KsService
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
        return $this->sdk->multiGetCampaignList($accounts, $page, $pageSize, $param);
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
            $param['start'] = Functions::getDate($option['date']);
            $param['end'] = Functions::getDate($option['date']);
        }


        $accountGroup = $this->getAccountGroup($accountIds);

        $t = microtime(1);

        $pageSize = 100;
        foreach($accountGroup as $g){
            $campaigns = $this->multiGetPageList($g, $pageSize, $param);

            Functions::consoleDump('count:'. count($campaigns));


            // 保存
            foreach($campaigns as $campaign) {
                $this->save($campaign);
            }
        }

        $t = microtime(1) - $t;
        var_dump($t);

        return true;
    }


    /**
     * @param $campaign
     * @return bool
     * 保存
     */
    public function save($campaign){
        $ksCampaignModel = new KsCampaignModel();
        $ksCampaign = $ksCampaignModel->where('id', $campaign['campaign_id'])->first();

        if(empty($ksCampaign)){
            $ksCampaign = new KsCampaignModel();
        }

        $ksCampaign->id = $campaign['campaign_id'];
        $ksCampaign->account_id = $campaign['account_id'];
        $ksCampaign->name = $campaign['campaign_name'];
        $ksCampaign->put_status = $campaign['put_status'];
        $ksCampaign->status = $campaign['status'];
        $ksCampaign->day_budget = $campaign['day_budget'];
        $ksCampaign->day_budget_schedule = $campaign['day_budget_schedule'];
        $ksCampaign->type = $campaign['campaign_type'];
        $ksCampaign->sub_type = $campaign['campaign_sub_type'];
        $ksCampaign->create_channel = $campaign['create_channel'];
        $ksCampaign->create_time = $campaign['create_time'];
        $ksCampaign->update_time = $campaign['update_time'];

        $ret = $ksCampaign->save();

        return $ret;
    }
}
