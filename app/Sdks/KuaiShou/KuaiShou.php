<?php

namespace App\Sdks\KuaiShou;


use App\Sdks\KuaiShou\Kernel\App;
use Exception;
use GuzzleHttp\Client;



/**
 * Class KuaiShou
 * @package App\Sdks\KuaiShou
 */
class KuaiShou extends App
{
    /**
     * @var KuaiShou 实例
     */
    protected static $instance;

    /**
     * api 版本
     * @var string
     */
    protected $apiVersion = 'v1';


    /**
     * 请求超时的秒数
     * @var int
     */
    protected $timeout = 30;


    /**
     * @var string access token
     */
    protected $accessToken = '';



    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array middleware list
     */
    protected $middleware = [];

    /**
     * @var array
     */
    protected $globalConfig = [];

    /**
     * @var array middleware list
     */
    protected $middlewareInstance = [];


    /**
     * @param string $accessToken
     * @return KuaiShou
     * @throws Exception
     */
    public static function init( string $accessToken = ''): KuaiShou
    {

        $instance = self::getInstance();
        $instance->setHeaders([
            'Content-Type'=>'application/json',
            'access-token'=> $accessToken
        ]);
        $instance->client = null;
        $instance->config = Config::getDefaultConfig();
        $instance->setAccessToken($accessToken);
        $instance->generateMiddlewareInstance();
        return $instance;
    }

    /**
     * Set guzzle client header
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): KuaiShou
    {
        if (empty($this->globalConfig['http_options'])) {
            $this->globalConfig['http_options'] = [];
        }
        $this->globalConfig['http_options']['headers'] = $headers;
        return $this;
    }

    /**
     * Get headers
     * @return array|mixed
     */
    public function getHeaders(): array
    {
        return empty($this->globalConfig['http_options']['headers']) ?[] : $this->globalConfig['http_options']['headers'];
    }

    /**
     * Set guzzle options
     * @param array $options
     * @return $this
     */
    public function setHttpOptions(array $options): KuaiShou
    {
        $this->globalConfig['http_options'] = $options;
        $this->client = new Client($this->globalConfig['http_options']);
        return $this;
    }

    /**
     * Get http options
     * @return array
     */
    public function getHttpOptions(): array
    {
        return empty($this->globalConfig['http_options']) ? [] : $this->globalConfig['http_options'];
    }


    /**
     * @return Client
     */
    public function getClient(): Client
    {
        if (empty($this->client)) {
            $httpOptions = $this->getHttpOptions();
            $config = array_merge($httpOptions,['timeout' => $this->timeout]);
            $this->client = new Client($config);
        }
        return $this->client;
    }

    /**
     * 生成中间件实例
     * @throws Exception
     */
    public function generateMiddlewareInstance()
    {
        $oldMiddlewareInstances = $this->middlewareInstance;

        $this->middlewareInstance = [];
        foreach ($this->middleware as $middlewareName) {
            if (empty($oldMiddlewareInstances[$middlewareName])) {
                if (!class_exists($middlewareName)) {
                    throw new Exception("中间件 {$middlewareName} 不存在");
                }
                $this->middlewareInstance[$middlewareName] = new $middlewareName();
                if (!method_exists($this->middlewareInstance[$middlewareName], 'handle')) {
                    throw new Exception("中间件 {$middlewareName} handle方法不存在");
                }
            } else {
                $this->middlewareInstance[$middlewareName] = $oldMiddlewareInstances[$middlewareName];
            }
        }
    }


    /**
     * Get instance
     * @return KuaiShou
     */
    public static function getInstance(): KuaiShou
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getGlobalConfig(): array
    {
        return $this->globalConfig;
    }

    /**
     * Set global config
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setGlobalConfig($key, $value): KuaiShou
    {
        $this->globalConfig[$key] = $value;
        return $this;
    }


    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }


    /**
     * Append a middleware
     * @param string $middleware
     * @throws Exception
     */
    public function appendMiddleware(string $middleware)
    {
        $this->middleware[] = $middleware;
        $this->generateMiddlewareInstance();
    }


    /**
     * Get all middleware instance
     * @return array
     */
    public function getMiddlewareInstance(): array
    {
        return $this->middlewareInstance;
    }


    /**
     * 设置accessToken
     * @param string $accessToken accessToken
     * @return $this
     */
    public function setAccessToken(string $accessToken): KuaiShou
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * 获取token
     * @return string token
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

}
