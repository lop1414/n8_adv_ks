<?php
namespace App\Sdks\KuaiShou\Container;


use App\Sdks\KuaiShou\Api\ProgramCreativeReportApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class ProgramCreativeApiContainer extends ApiContainer
{

    /** @var ProgramCreativeReportApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): ProgramCreativeApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new ProgramCreativeReportApi($client, $app->getConfig());
        return $this;
    }



    public function get(array $params = [])
    {
        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->get($params);
        });
    }



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
