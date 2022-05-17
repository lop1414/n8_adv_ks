<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;


class MultipleCampaignApi extends MultipleApi
{


    public function get($params = []): array
    {
        $resourcePath = '/v1/campaign/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }


}
