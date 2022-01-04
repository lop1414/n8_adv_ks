<?php

namespace App\Sdks\Ks\Traits;

trait Multi
{
    /**
     * @param $url
     * @param array $accounts
     * @param int $page
     * @param int $pageSize
     * @param array $param
     * @return mixed
     * 并发获取分页列表
     */
    public function multiGetPageList($url, array $accounts, $page = 1, $pageSize = 10, $param = []){
        $curlOptions = [];
        foreach($accounts as $account){
            $p = array_merge([
                'advertiser_id' => $account['account_id'],
                'page' => $page,
                'page_size' => $pageSize,
                'time_filter_type' => 1
            ], $param);

            $curlOptions[] = [
                'url' => $url,
                'param' => $p,
                'method' => 'POST',
                'header' => [
                    'Access-Token:'. $account['access_token'],
                    'Content-Type: application/json; charset=utf-8',
                ]
            ];
        }

        return $this->multiPublicRequest($curlOptions);
    }
}
