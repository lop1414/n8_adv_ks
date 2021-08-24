<?php

namespace App\Models\Ks\Report;

use App\Models\Ks\KsModel;

class KsAccountReportModel extends KsModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ks_account_reports';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
