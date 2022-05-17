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


    /**
     * @param KuaiShou $app
     * @param Client $client
     * @return AdUnitApiContainer
     */
    public function init(KuaiShou $app, Client $client): AdUnitApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new AdUnitApi($client, $app->getConfig());
        return $this;
    }

    /**
     * @param int $advertiserId
     * @param array $params
     * @return mixed
     */
    public function get(int $advertiserId,array $params = [])
    {
        $params['advertiser_id'] = $advertiserId;

        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            $advertiserId = $params['advertiser_id'];

            return $this->apiInstance->get($advertiserId,$params);
        });
    }

}
