<?php

namespace App\Sdks\Ks\Traits;

trait Creative
{


    /**
     * @param array $accounts
     * @param int $page
     * @param int $pageSize
     * @param array $param
     * @return mixed
     * 并发获取创意列表
     */
    public function multiGetCreativeList(array $accounts, $page = 1, $pageSize = 10, $param = []){
        $url = $this->getUrl('v1/creative/list');

        return $this->multiGetPageList($url, $accounts, $page, $pageSize, $param);
    }


    /**
     * @param array $accounts
     * @param int $page
     * @param int $pageSize
     * @param array $param
     * @return mixed
     * 并发获取程序化创意列表
     */
    public function multiGetPackageCreativeList(array $accounts, $page = 1, $pageSize = 10, $param = []){
        $url = $this->getUrl('v2/creative/advanced/program/list');

        return $this->multiGetPageList($url, $accounts, $page, $pageSize, $param);
    }

}
