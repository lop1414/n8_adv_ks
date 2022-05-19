<?php

namespace App\Sdks\KuaiShou\Kernel;

use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use App\Sdks\KuaiShou\KuaiShou;
use Exception;
use GuzzleHttp\Client;


class ApiContainer
{
    /**
     * @var KuaiShou
     */
    protected $app;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var BaseObject Api instance
     */
    protected $apiInstance;

    /**
     * 忽略的中间件
     * @var array
     */
    protected $skipMiddleware = [];


    public function init(KuaiShou $app, Client $client)
    {
        $this->app = $app;
        $this->client = $client;

        return $this;
    }

    /**
     * Handle middleware with pipeline
     * 可参考：Laravel pipeline
     *
     * @param $method
     * @param $params
     * @param $next
     *
     * @return mixed
     */
    public function handleMiddleware($method, $params, $next)
    {
        $request = MiddlewareRequest::init($this->app, get_class($this->apiInstance), $method, $params);
        $middlewareFun = [];
        foreach ($this->app->getMiddlewareInstance() as $middlewareName => $middleware) {
            if ($this->hasSkipMiddleware($middlewareName, $method)) {
                continue;
            }
            array_unshift($middlewareFun, [$middleware, 'handle']);
        }

        $func = array_reduce($middlewareFun, function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                return call_user_func($pipe, $passable, $stack);
            };
        }, $next);

        return $func($request);
    }



    public function hasSkipMiddleware($middlewareName,$method): bool
    {

        $configGlobal = $this->app->getGlobalConfig();
        $apiInstanceClassName = get_class($this->apiInstance);
        if (in_array($middlewareName, $this->skipMiddleware)) {
            return true;
        }

        if (!empty($configGlobal['app'][$apiInstanceClassName][$method]['skip_middleware'])) {
            if (in_array($middlewareName, $configGlobal['app'][$apiInstanceClassName][$method]['skip_middleware'],
                true)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Handle api response
     * @param $response
     * @return mixed
     * @throws Exception
     */
    public function handleResponse($response)
    {
        $response = json_decode($response,true);
        if (!isset($response['code'])) {
            throw new Exception("API response has not code field.");
        }
        if ($response['code'] != 0) {
            throw new Exception('api error message : '.$response['msg'],$response['code']);
        }

        return $response['data'] ?? null;
    }
}
