<?php
namespace App\Sdks\KuaiShou\Container;

use App\Sdks\KuaiShou\Api\AdUnitApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class AdUnitApiContainer extends ApiContainer
{

    /** @var AdUnitApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): AdUnitApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new AdUnitApi($client, $app->getConfig());
        return $this;
    }



    public function get(array $params = []): array
    {

        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();

            return $this->apiInstance->get($params);
        });
    }



    public function multipleGet(array $advertiserIds, array $params = []): array
    {

        $tmpParams = [];
        foreach ($advertiserIds as $advertiserId){
            $tmpParams[] = array_merge($params,['advertiser_id' => $advertiserId]);
        }

        return $this->handleMiddleware('get', $tmpParams, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->multipleGet($params);
        });
    }
}
