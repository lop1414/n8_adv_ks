<?php
namespace App\Sdks\KuaiShou\Container;

use App\Sdks\KuaiShou\Api\CampaignApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class CampaignApiContainer extends ApiContainer
{

    /** @var CampaignApi */
    public $apiInstance;


    /**
     * @param KuaiShou $app
     * @param Client $client
     * @return CampaignApiContainer
     */
    public function init(KuaiShou $app, Client $client): CampaignApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new CampaignApi($client, $app->getConfig());
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
