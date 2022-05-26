<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Models\Ks\KsCampaignModel;
use App\Sdks\KuaiShou\KuaiShou;
use App\Services\KuaiShouService;

class KsCampaignService extends BaseService
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
                $campaigns = KuaiShouService::multiGet($ksSdk->campaign(),$accountIds,$param);
                foreach($campaigns as $campaign) {
                    $this->save($campaign);
                }
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
    public function save($campaign): bool
    {
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

        return $ksCampaign->save();
    }
}
