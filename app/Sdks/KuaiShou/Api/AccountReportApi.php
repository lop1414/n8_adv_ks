<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;

/**
 * 账户报表
 * Class AccountReportApi
 * @package App\Sdks\KuaiShou\Api
 */
class AccountReportApi extends MultipleApi
{

    public function get(array $param): array
    {
        // 验证参数
        $requiredParam = ['advertiser_id'];
        $this->checkRequiredParam($requiredParam,$param);

        // 构建Request对象
        $resourcePath = '/v1/report/account_report';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($param);
        $request =  new Request('POST', $uri,$headers,$httpBody);

        // 请求
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/report/account_report';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }
}
