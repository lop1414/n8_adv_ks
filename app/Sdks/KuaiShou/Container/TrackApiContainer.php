<?php
namespace App\Sdks\KuaiShou\Container;

use App\Sdks\KuaiShou\Api\TrackApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class TrackApiContainer extends ApiContainer
{

    /** @var TrackApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): TrackApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new TrackApi($client, $app->getConfig());
        return $this;
    }



    public function activate(array $params = []): array
    {

        return $this->handleMiddleware('activate', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();

            return $this->apiInstance->activate($params);
        });
    }


}
