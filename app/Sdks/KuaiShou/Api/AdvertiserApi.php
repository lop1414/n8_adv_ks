<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;
use GuzzleHttp\Psr7\Request;


class AdvertiserApi extends MultipleApi
{


    public function get($advertiserId): array
    {
        $request = $this->getRequest($advertiserId);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }

    /**
     * @param int|null $advertiserId
     * @return Request
     */
    protected function getRequest(int $advertiserId): Request
    {
        $resourcePath = '/gw/uc/v1/advertisers';
        $queryParams = [];
        $queryParams['advertiser_id'] = $advertiserId;

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParams);
        return new Request('POST', $uri,$headers,$httpBody);
    }
}
