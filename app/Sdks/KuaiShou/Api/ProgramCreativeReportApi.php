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

    public function get(int $advertiserId,string $startDateMin,string $endDateMin,array $param): array
    {
        $request = $this->getRequest($advertiserId,$startDateMin,$endDateMin,$param);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    protected function getRequest(int $advertiserId,string $startDateMin,string $endDateMin, array $param = []): Request
    {
        $resourcePath = '/v1/report/program_creative_report';
        $queryParam = $param;
        $queryParam['advertiser_id'] = $advertiserId;
        $queryParam['start_date_min'] = $startDateMin;
        $queryParam['end_date_min'] = $endDateMin;

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParam);
        return new Request('POST', $uri,$headers,$httpBody);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/report/program_creative_report';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }
}
