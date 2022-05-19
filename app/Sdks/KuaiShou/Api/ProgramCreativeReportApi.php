<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;

/**
 * 程序化创意报表
 * Class ProgramCreativeReportApi
 * @package App\Sdks\KuaiShou\Api
 */
class ProgramCreativeReportApi extends MultipleApi
{

    public function get(array $param): array
    {
        $request = $this->getRequest($param);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    protected function getRequest(array $param = []): Request
    {
        $requiredParam = ['advertiser_id','start_date_min','end_date_min'];
        $this->checkRequiredParam($requiredParam,$param);

        $resourcePath = '/v1/report/program_creative_report';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($param);
        return new Request('POST', $uri,$headers,$httpBody);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/report/program_creative_report';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }
}
