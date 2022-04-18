<?php

namespace App\Sdks\Ks\Traits;

trait Report
{
    public function multiGetAccountReportList(array $accounts, $page = 1, $pageSize = 10, $param = []){
        $url = $this->getUrl('v1/report/account_report');

        return $this->multiGetPageList($url, $accounts, $page, $pageSize, $param);
    }

    /**
     * @param array $accounts
     * @param int $page
     * @param int $pageSize
     * @param array $param
     * @return mixed
     * 并发获取创意报表
     */
    public function multiGetCreativeReportList(array $accounts, $page = 1, $pageSize = 10, $param = []){
        $url = $this->getUrl('v1/report/creative_report');

        return $this->multiGetPageList($url, $accounts, $page, $pageSize, $param);
    }

    /**
     * @param array $accounts
     * @param int $page
     * @param int $pageSize
     * @param array $param
     * @return mixed
     * 并发获取程序化创意报表
     */
    public function multiGetProgramCreativeReportList(array $accounts, $page = 1, $pageSize = 10, $param = []){
        $url = $this->getUrl('v1/report/program_creative_report');

        return $this->multiGetPageList($url, $accounts, $page, $pageSize, $param);
    }

    /**
     * @param array $accounts
     * @param int $page
     * @param int $pageSize
     * @param array $param
     * @return mixed
     * 并发获取素材报表
     */
    public function multiGetMaterialReportList(array $accounts, $page = 1, $pageSize = 10, $param = []){
        $url = $this->getUrl('v1/report/material_report');

        return $this->multiGetPageList($url, $accounts, $page, $pageSize, $param);
    }
}
