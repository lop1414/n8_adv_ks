<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;

/**
 * 程序化创意
 * Class ProgramCreativeApi
 * @package App\Sdks\KuaiShou\Api
 */
class ProgramCreativeApi extends MultipleApi
{

    public function get(int $advertiserId,array $param): array
    {
        $request = $this->getRequest($advertiserId,$param);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    protected function getRequest(int $advertiserId, array $param = []): Request
    {
        $resourcePath = '/v2/creative/advanced/program/list';
        $queryParam = $param;
        $queryParam['advertiser_id'] = $advertiserId;

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParam);
        return new Request('POST', $uri,$headers,$httpBody);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v2/creative/advanced/program/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }


}
