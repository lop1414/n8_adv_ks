<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;

/**
 * 图片
 * Class AdUnitApi
 * @package App\Sdks\KuaiShou\Api
 */
class ImageApi extends MultipleApi
{

    public function get(array $params): array
    {
        // 验证参数
        $requiredParam = ['advertiser_id'];
        $this->checkRequiredParam($requiredParam,$params);

        // 构建Request对象
        $resourcePath = '/v1/file/ad/image/list';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($params);
        $request = new Request('POST', $uri,$headers,$httpBody);

        // 请求
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/file/ad/image/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }


    public function upload(array $params = []): array
    {
        // 验证参数
        $requiredParam = ['advertiser_id','file','signature'];
        $this->checkRequiredParam($requiredParam,$params);

        // 构建Request对象
        $resourcePath = '/v2/file/ad/image/upload';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];

        $httpBody = new MultipartStream($this->makeMultipartContents($params));

        $request = new Request('POST', $uri,$headers,$httpBody);

        $response = $this->client->send($request);

        return $this->handleResponse($response);
    }
}
