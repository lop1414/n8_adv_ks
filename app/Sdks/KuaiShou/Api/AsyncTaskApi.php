<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;

/**
 * 异步任务数据报表
 * Class AsyncTaskApi
 * @package App\Sdks\KuaiShou\Api
 */
class AsyncTaskApi extends MultipleApi
{

    public function get(array $param): array
    {
        // 验证参数
        $requiredParam = ['advertiser_id'];
        $this->checkRequiredParam($requiredParam,$param);

        // 构建Request对象
        $resourcePath = '/v1/async_task/list';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($param);
        $request = new Request('POST', $uri,$headers,$httpBody);

        // 请求
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/async_task/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }


    public function create(array $param = []): array
    {
        // 验证参数
        $requiredParam = ['advertiser_id', 'task_name', 'task_params'];
        $this->checkRequiredParam($requiredParam,$param);

        // 构建Request对象
        $resourcePath = '/v1/async_task/create';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($param);
        $request = new Request('POST', $uri,$headers,$httpBody);

        // 请求
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    public function download(array $param = []): string
    {
        // 验证参数
        $requiredParam = ['advertiser_id', 'task_id'];
        $this->checkRequiredParam($requiredParam,$param);

        // 构建Request对象
        $resourcePath = '/v1/async_task/download';
        $uri = $this->config->getHost() . $resourcePath. '?' .http_build_query($param);
        $headers = [];
        $httpBody = json_encode($param);
        $request = new Request('GET', $uri,$headers,$httpBody);

        // 请求
        $response = $this->client->send($request);
        return $response->getBody()->getContents();
    }
}
