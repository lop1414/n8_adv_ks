<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\Api;
use GuzzleHttp\Psr7\Request;


/**
 * 转化归因
 * Class TrackApi
 * @package App\Sdks\KuaiShou\Api
 */
class TrackApi extends Api
{

    public function activate(array $params): array
    {
        // 验证参数
        $requiredParam = ['event_type','event_time','callback'];
        $this->checkRequiredParam($requiredParam,$params);

        $callback = $params['callback'];
        unset($params['callback']);

        // 构建Request对象
        $uri = $callback.'&'.http_build_query($params);
        $request = new Request('GET', $uri);

        // 请求
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }


}
