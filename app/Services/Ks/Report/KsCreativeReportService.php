<?php

namespace App\Services\Ks\Report;

use App\Models\Ks\Report\KsCreativeReportModel;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;

class KsCreativeReportService extends KsReportService
{


    public function setModelClass(): bool
    {
        $this->modelClass = KsCreativeReportModel::class;
        return true;
    }


    public function getContainer(KuaiShou $ksSdk): ApiContainer
    {
        return $ksSdk->creativeReport();
    }
}
