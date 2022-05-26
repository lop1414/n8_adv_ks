<?php
namespace App\Sdks\KuaiShou\Container;

use App\Sdks\KuaiShou\Api\AdvertiserApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use App\Sdks\KuaiShou\KuaiShou;
use GuzzleHttp\Client;


class AdvertiserApiContainer extends ApiContainer
{

    /** @var AdvertiserApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): AdvertiserApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new AdvertiserApi($client, $app->getConfig());
        return $this;
    }



    public function get(array $params): array
    {
        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();

            return $this->apiInstance->get($params);
        });
    }


    
    public function info(array $params): array
    {
        return $this->handleMiddleware('info', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();

            return $this->apiInstance->get($params);
        });
    }
}
