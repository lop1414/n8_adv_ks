<?php

namespace App\Sdks\KuaiShou\Kernel;

use App\Sdks\KuaiShou\Config;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class Api
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ClientInterface
     */
    protected $client;


    /**
     * AdvertiserApi constructor.
     * @param ClientInterface|null $client
     * @param Config|null $config
     */
    public function __construct(
        ClientInterface $client = null,
        Config $config = null
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Config();
    }


    public function getConfig(): Config
    {
        return $this->config;
    }



    /**
     * @param $response
     * @throws Exception
     */
    public function handleResponse($response){
        $statusCode = $response->getStatusCode();

        if ($statusCode != 200 ) {
            throw new Exception('API 请求异常',$statusCode);
        }
    }
}
