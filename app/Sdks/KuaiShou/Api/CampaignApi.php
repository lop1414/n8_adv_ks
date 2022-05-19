<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;


class CampaignApi extends MultipleApi
{

    public function get($advertiserId,$param): array
    {
        $request = $this->getRequest($advertiserId,$param);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }


    /**
     * 广告计划列表请求
     * @param int $advertiserId
     * @param array $param
     * @return Request
     */
    protected function getRequest(int $advertiserId, array $param = []): Request
    {
        $resourcePath = '/v1/campaign/list';
        $queryParam = $param;
        $queryParam['advertiser_id'] = $advertiserId;

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParam);
        return new Request('POST', $uri,$headers,$httpBody);
    }

    /**
     * 批量获取
     * @param array $params
     * @return array
     */
    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/campaign/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }

}
