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
        return $this->handleMiddleware('multipleGet', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();

            return $this->apiInstance->get($params);
        });
    }



    public function info(array $params): array
    {
        return $this->handleMiddleware('info', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();

            return $this->apiInstance->info($params);
        });
    }


    /**
     * 批量获取广告账户流水信息
     * @param array $advertiserIds
     * @param array $params
     * @return array
     */
    public function multipleGet(array $advertiserIds, array $params = []): array
    {
        $tmpParams = [];
        foreach ($advertiserIds as $advertiserId){
            $tmpParams[] = array_merge($params,['advertiser_id' => $advertiserId]);
        }

        return $this->handleMiddleware('multipleGet', $tmpParams, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->multipleGetFundDailyFlows($params);
        });
    }
}
