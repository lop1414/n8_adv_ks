<?php

namespace App\Sdks\KuaiShou\Middleware;

use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use Closure;
use Exception;


class HandleResponseMiddleware implements MiddlewareInterface
{


    /**
     * 响应结果处理
     *
     * @param MiddlewareRequest $request
     * @param Closure $next
     * @return mixed|null
     * @throws Exception
     */
    public function handle(MiddlewareRequest $request, Closure $next)
    {

        $response = $next($request);

        $response = json_decode($response,true);
        if (!isset($response['code'])) {
            throw new Exception("API response has not code field.");
        }

        if ($response['code'] != 0) {
            throw new Exception('api error message : '.$response['message'],$response['code']);
        }

        return $response['data'] ?? null;
    }
}
