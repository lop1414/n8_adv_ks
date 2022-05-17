<?php
namespace App\Sdks\KuaiShou\Container;


use App\Sdks\KuaiShou\Api\MultipleCampaignApi;
use App\Sdks\KuaiShou\Kernel\MultipleApiContainer;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use App\Sdks\KuaiShou\KuaiShou;
use GuzzleHttp\Client;


class MultipleCampaignApiContainer extends MultipleApiContainer
{
    /** @var MultipleCampaignApi */
    public $apiInstance;


    /**
     * @param KuaiShou $app
     * @param Client $client
     * @return MultipleCampaignApiContainer
     */
    public function init(KuaiShou $app, Client $client): MultipleCampaignApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new MultipleCampaignApi($client, $app->getConfig());
        return $this;
    }



    /**
     * @param array $advertiserIds
     * @param array $param
     * @return mixed
     */
    public function get(array $advertiserIds, array $param)
    {
        $params = [];
        foreach ($advertiserIds as $advertiserId){
            $params[] = array_merge($param,['advertiser_id' => $advertiserId]);
        }

        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->get($params);
        });
    }



}
