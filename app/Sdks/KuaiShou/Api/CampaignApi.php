<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\Api;
use GuzzleHttp\Psr7\Request;


class CampaignApi extends Api
{

    public function get($advertiserId,$param): string
    {
        $request = $this->getRequest($advertiserId,$param);
        $response = $this->client->send($request);
        $this->handleResponse($response);
        return $response->getBody()->getContents();
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
        $queryParam = array_merge($param,['advertiser_id' => $advertiserId]);

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParam);
        return new Request('POST', $uri,$headers,$httpBody);
    }


}
