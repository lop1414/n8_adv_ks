<?php

namespace App\Services\Ks\Report;

use App\Models\Ks\Report\KsAccountFundDailyFlowModel;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;

class KsAccountFundDailyFlowService extends KsReportService
{

    public $modelClass = KsAccountFundDailyFlowModel::class;


    public function getContainer(KuaiShou $ksSdk): ApiContainer
    {
        return $ksSdk->advertiser();
    }


    /**
     * api 请求参数
     * @return array
     */
    public function getApiReqParams(): array
    {
        return [];
    }


    /**
     * @param $item
     * @return bool
     * 校验
     */
    protected function itemValid($item): bool
    {
        $valid = true;

        if(
            empty($item['balance']) &&
            empty($item['daily_charge']) &&
            empty($item['real_charged']) &&
            empty($item['contract_rebate_real_charged']) &&
            empty($item['direct_rebate_real_charged'])&&
            empty($item['daily_transfer_in'])&&
            empty($item['real_recharged'])&&
            empty($item['contract_rebate_real_recharged'])&&
            empty($item['direct_rebate_real_recharged'])&&
            empty($item['daily_transfer_out'])
        ){
            $valid = false;
        }

        return $valid;
    }




    protected function itemFilter(&$item){
        $item['charge'] = $item['daily_charge'];
        $item['date_time'] = $item['date'].' 00:00:00';
        $item['daily_charge'] = bcmul($item['daily_charge'],1000);
        $item['real_charged'] = bcmul($item['real_charged'],1000);
        $item['contract_rebate_real_charged'] = bcmul($item['contract_rebate_real_charged'],1000);
        $item['direct_rebate_real_charged'] = bcmul($item['direct_rebate_real_charged'],1000);
        $item['daily_transfer_in'] = bcmul($item['daily_transfer_in'],1000);
        $item['daily_transfer_out'] = bcmul($item['daily_transfer_out'],1000);
        $item['balance'] = bcmul($item['balance'],1000);
        $item['real_recharged'] = bcmul($item['real_recharged'],1000);
        $item['contract_rebate_real_recharged'] = bcmul($item['contract_rebate_real_recharged'],1000);
        $item['direct_rebate_real_recharged'] = bcmul($item['direct_rebate_real_recharged'],1000);
    }
}
