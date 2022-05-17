<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\Api;
use GuzzleHttp\Psr7\Request;


class AdvertiserApi extends Api
{


    public function get($advertiserId): string
    {
        $request = $this->getRequest($advertiserId);
        $response = $this->client->send($request);
        $this->handleResponse($response);
        return $response->getBody()->getContents();
    }

    /**
     * @param int|null $advertiserId
     * @return Request
     */
    protected function getRequest(int $advertiserId): Request
    {
        $resourcePath = '/gw/uc/v1/advertisers';
        $queryParams = [];
        $advertiserId !== null && $queryParams['advertiser_id'] = $advertiserId;

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParams);
        return new Request('POST', $uri,$headers,$httpBody);
    }
}
