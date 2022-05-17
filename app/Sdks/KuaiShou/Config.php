<?php

namespace App\Sdks\KuaiShou;


class Config
{
    private static $defaultConfig;

    /**
     * api全局参数
     * @var string[]
     */
    protected $apiParam = [];


    /**
     * The host
     *
     * @var string
     */
    protected $host = 'https://ad.e.kuaishou.com/rest/openapi';



    /**
     * 设置 API key
     * @param string $key
     * @param string $val
     * @return $this
     */
    public function setApiParam(string $key, string $val): Config
    {
        $this->apiParam[$key] = $val;
        return $this;
    }

    /**
     * 获取指定下标API参数
     * @param string $key API参数下标
     * @return string
     */
    public function getApiParam(string $key): ?string
    {
        return $this->apiParam[$key] ?? null;
    }


    /**
     * 设置host
     * @param string $host Host
     * @return $this
     */
    public function setHost(string $host): Config
    {
        $this->host = $host;
        return $this;
    }

    /**
     * 获取host
     * @return string Host
     */
    public function getHost(): string
    {
        return $this->host;
    }


    /**
     * 获取 DefaultConfig 实例
     * @return Config
     */
    public static function getDefaultConfig(): Config
    {
        if (self::$defaultConfig === null) {
            self::$defaultConfig = new Config();
        }

        return self::$defaultConfig;
    }

    /**
     * 设置 DefaultConfig 实例
     * @param Config $config 配置对象的实例
     * @return void
     */
    public static function setDefaultConfig(Config $config)
    {
        self::$defaultConfig = $config;
    }




}
