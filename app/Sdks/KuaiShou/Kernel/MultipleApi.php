<?php

namespace App\Sdks\KuaiShou\Kernel;

use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class MultipleApi extends Api
{
    /**
     * 并发请求个数
     * @var int
     */
    protected $concurrency = 3;

    /**
     * 响应数据
     * @var array
     */
    private $multipleResponseData = [];


    /**
     * 请求参数
     * @var array
     */
    private $requestParams = [];


    public function setConcurrency(int $i): MultipleApi
    {
        $this->concurrency = $i;
        return $this;
    }

    public function getConcurrency(): int
    {
        return $this->concurrency;
    }



    /**
     * @param string $uri
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return array
     */
    public function multipleRequest(string $uri,array $params,string $method,array $headers = []): array
    {
        $this->clearResponseData();
        $this->requestParams = $params;

        $request = $this->getMultipleRequest($uri, $params, $method, $headers);

        $pool = new Pool($this->client, $request, [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) {
                // 请求成功
                $content = $response->getBody()->getContents();
                $this->setMultipleResponseData($index,json_decode($content,true));
            },
            'rejected' => function ($reason, $index) {
                // 失败
                $err = [
                    'code'    => $reason->getCode(),
                    'message' => $reason->getMessage()
                ];
                $this->setMultipleResponseData($index,$err);
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        return $this->multipleResponseData;
    }



    /**
     * 获取请求对象
     * @param string $uri
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return \Generator
     */
    protected function getMultipleRequest(string $uri, array $params, string $method, array $headers = []): \Generator
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
    protected function setMultipleResponseData($key,$data): bool
    {
        $this->multipleResponseData[$key] = $data;
        $this->multipleResponseData[$key]['request_params'] = $this->requestParams[$key];
        return true;
    }


    /**
     * 清除响应数据
     * @return bool
     */
    protected function clearResponseData(): bool
    {
        $this->multipleResponseData = [];
        return true;
    }
}
