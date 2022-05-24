<?php
namespace App\Sdks\KuaiShou\Container;


use App\Sdks\KuaiShou\Api\AsyncTaskApi;
use App\Sdks\KuaiShou\Helper\Csv;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use App\Sdks\KuaiShou\KuaiShou;
use GuzzleHttp\Client;


class AsyncTackApiContainer extends ApiContainer
{

    /** @var AsyncTaskApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): AsyncTackApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new AsyncTaskApi($client, $app->getConfig());
        return $this;
    }



    public function get(array $params): array
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


    public function create($params): array
    {
        return $this->handleMiddleware('create', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            return $this->apiInstance->create($params);
        });
    }


    /**
     * 获取任务csv文件数据
     * @param array $params
     * @return mixed
     */
    public function getDownloadData(array $params): array
    {
        return $this->handleMiddleware('get', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            $csv =  $this->apiInstance->download($params);
            return Csv::getCsvData($csv);
        });
    }
}
