<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;


/**
 * 素材报表
 * Class MaterialReportApi
 * @package App\Sdks\KuaiShou\Api
 */
class MaterialReportApi extends MultipleApi
{

    public function get(array $param): array
    {
        $request = $this->getRequest($param);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    protected function getRequest( array $param = []): Request
    {
        $requiredParam = ['advertiser_id','start_date','end_date','view_type'];
        $this->checkRequiredParam($requiredParam,$param);

        $resourcePath = '/v1/report/material_report';
        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($param);
        return new Request('POST', $uri,$headers,$httpBody);
    }



    public function multipleGet(array $params = []): array
    {
        $resourcePath = '/v1/report/material_report';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }
}
