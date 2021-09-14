<?php

namespace App\Services\Ks\Report;

use App\Common\Tools\CustomException;
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
            if(isset($creativeReportMap[$accountId]) && bcsub($creativeReportMap[$accountId] * 100000, $charge * 100000) >= 0){
                continue;
            }
            $creativeAccountIds[] = $accountId;
        }

        return $creativeAccountIds;
    }
}
