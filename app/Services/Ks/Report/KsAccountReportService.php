<?php

namespace App\Services\Ks\Report;

use App\Models\Ks\Report\KsAccountReportModel;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;

class KsAccountReportService extends KsReportService
{


    public function setModelClass(): bool
    {
        $this->modelClass = KsAccountReportModel::class;
        return true;
    }


    public function getContainer(KuaiShou $ksSdk): ApiContainer
    {
        return $ksSdk->accountReport();
    }
}
