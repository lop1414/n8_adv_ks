<?php

namespace App\Sdks\KuaiShou\Kernel;


use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class MultipleApi extends Api
{

    /**
     * 响应数据
     * @var array
     */
    private $responseData = [];

    /**
     * 请求参数
     * @var array
     */
    private $requestParams = [];


    /**
     * 并发请求个数
     * @var int
     */
    private $concurrency = 5;



    public function multipleRequest($uri, $params, $method, $headers = []): array
    {
        $this->clearResponseData();
        $this->requestParams = $params;

        $request = $this->getMultipleRequest($uri, $params, $method, $headers);

        $pool = new Pool($this->client, $request, [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) {
                // 请求成功
                $content = $response->getBody()->getContents();
                $this->setResponseData($index,json_decode($content,true));
            },
            'rejected' => function ($reason, $index) {
                // 失败
                $err = [
                    'code'    => $reason->getCode(),
                    'message' => $reason->getMessage()
                ];
                $this->setResponseData($index,$err);
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        return $this->responseData;
    }



    protected function getMultipleRequest($uri, $params, $method, $headers = []): \Generator
    {
        foreach ($params as $param) {
            $httpBody = json_encode($param);
            yield new Request($method, $uri,$headers, $httpBody);
        }
    }




    /**
     * 设置响应成功数据
     * @param $key
     * @param $data
     * @return bool
     */
    protected function setResponseData($key,$data): bool
    {
        $this->responseData[$key] = $data;
        $this->responseData[$key]['request_params'] = $this->requestParams[$key];
        return true;
    }


    /**
     * 清除响应数据
     * @return bool
     */
    protected function clearResponseData(): bool
    {
        $this->responseData = [];
        return true;
    }
}
