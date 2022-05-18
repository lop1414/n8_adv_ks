<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\MultipleApi;


class MultipleAdUnitApi extends MultipleApi
{


    public function get($params = []): array
    {
        $resourcePath = '/v1/ad_unit/list';
        $uri = $this->config->getHost() . $resourcePath;

        return $this->multipleRequest($uri, $params, 'POST');
    }


}
