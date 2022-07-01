<?php
namespace App\Sdks\KuaiShou\Container;


use App\Sdks\KuaiShou\Api\VideoApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class VideoApiContainer extends ApiContainer
{

    /** @var VideoApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): VideoApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new VideoApi($client, $app->getConfig());
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
     * 推送视频
     * @param array $params
     * @return mixed
     */
    public function share(array $params = []): array
    {
        return $this->handleMiddleware('share', $params, function(MiddlewareRequest $request) {
            $params = $request->getApiMethodParams();
            return $this->apiInstance->share($params);
        });
    }

    /**
     * 上传视频
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
