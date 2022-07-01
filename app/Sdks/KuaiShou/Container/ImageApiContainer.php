<?php
namespace App\Sdks\KuaiShou\Container;


use App\Sdks\KuaiShou\Api\ImageApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class ImageApiContainer extends ApiContainer
{

    /** @var ImageApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): ImageApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new ImageApi($client, $app->getConfig());
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


    /**
     * 上传图片
     * @param array $params
     * @return mixed
     */
    public function upload(array $params = []): array
    {
        return $this->handleMiddleware('upload', $params, function(MiddlewareRequest $request) {
            $params = $request->getApiMethodParams();
            return $this->apiInstance->upload($params);
        });
    }
}
