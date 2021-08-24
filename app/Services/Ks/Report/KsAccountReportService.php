<?php

namespace App\Services\Ks\Report;

use App\Models\Ks\Report\KsAccountReportModel;

class KsAccountReportService extends KsReportService
{
    /**
     * OceanAccountReportService constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);

        $this->modelClass = KsAccountReportModel::class;
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
        return $this->sdk->multiGetAccountReportList($accounts, $page, $pageSize, $param);
    }
}
