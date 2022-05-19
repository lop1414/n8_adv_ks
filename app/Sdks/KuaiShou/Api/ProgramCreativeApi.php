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

    public function get(array $param): array
    {
        // 验证参数
        $requiredParam = ['advertiser_id'];
        $this->checkRequiredParam($requiredParam,$param);

        // 构建Request对象
        $resourcePath = '/v2/creative/advanced/program/list';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($param);
        $request = new Request('POST', $uri,$headers,$httpBody);

        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v2/creative/advanced/program/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }


}
