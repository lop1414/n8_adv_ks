<?php

namespace App\Models\Ks\Report;


class KsAccountFundDailyFlowModel extends KsReportModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_account_fund_daily_flows';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
