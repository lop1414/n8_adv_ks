<?php
namespace App\Sdks\KuaiShou\Container;

use App\Sdks\KuaiShou\Api\CreativeApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class CreativeApiContainer extends ApiContainer
{

    /** @var CreativeApi */
    public $apiInstance;


    /**
     * @param KuaiShou $app
     * @param Client $client
     * @return CreativeApiContainer
     */
    public function init(KuaiShou $app, Client $client): CreativeApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new CreativeApi($client, $app->getConfig());
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

    /**
     * @param array $advertiserIds
     * @param array $param
     * @return mixed
     */
    public function multipleGet(array $advertiserIds, array $param)
    {
        $params = [];
        foreach ($advertiserIds as $advertiserId){
            $params[] = array_merge($param,['advertiser_id' => $advertiserId]);
        }

        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->multipleGet($params);
        });
    }

}
