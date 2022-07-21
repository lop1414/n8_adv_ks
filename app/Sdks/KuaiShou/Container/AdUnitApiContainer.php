<?php
namespace App\Sdks\KuaiShou\Container;

use App\Sdks\KuaiShou\Api\AdUnitApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use Exception;
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

        return $this->handleMiddleware('multipleGet', $tmpParams, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->multipleGet($params);
        });
    }


    public function updateStatus(int $advertiserId, array $unitIds, int $status)
    {
        if(!in_array($status,[1,2,3])){
            throw new Exception("status 值不合法.");
        }

        $params = [
            'advertiser_id' => $advertiserId,
            'unit_ids'      => $unitIds,
            'put_status'    => $status
        ];

        return $this->handleMiddleware('update_status', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->updateStatus($params);
        });
    }
}
