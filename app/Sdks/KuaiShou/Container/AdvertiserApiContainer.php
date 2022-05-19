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



    public function get(int $advertiserId)
    {
        $params['advertiser_id'] = $advertiserId;

        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            $advertiserId = $params['advertiser_id'];

            return $this->apiInstance->get($advertiserId);
        });
    }
}
