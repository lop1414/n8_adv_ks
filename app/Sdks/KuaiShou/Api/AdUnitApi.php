<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;

/**
 * 广告组
 * Class AdUnitApi
 * @package App\Sdks\KuaiShou\Api
 */
class AdUnitApi extends MultipleApi
{

    public function get(int $advertiserId,array $param): array
    {
        $request = $this->getRequest($advertiserId,$param);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    protected function getRequest(int $advertiserId, array $param = []): Request
    {
        $resourcePath = '/v1/ad_unit/list';
        $queryParam = $param;
        $queryParam['advertiser_id'] = $advertiserId;

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParam);
        return new Request('POST', $uri,$headers,$httpBody);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/ad_unit/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }
}
