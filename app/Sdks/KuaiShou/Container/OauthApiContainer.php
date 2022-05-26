<?php
namespace App\Sdks\KuaiShou\Container;

use App\Sdks\KuaiShou\Api\OauthApi;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use GuzzleHttp\Client;


class OauthApiContainer extends ApiContainer
{

    /** @var OauthApi */
    public $apiInstance;


    public function init(KuaiShou $app, Client $client): OauthApiContainer
    {
        parent::init($app, $client);
        $this->apiInstance = new OauthApi($client, $app->getConfig());
        return $this;
    }

    /**
     * 获取授权链接
     * @param array $params
     * @return string
     */
    public function authorize(array $params = []): string
    {
        return $this->handleMiddleware('authorize', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            $appId = $params['app_id'] ?? null;
            $state = $params['state'] ?? null;
            $redirectUri = $params['redirect_uri'] ?? null;

            return $this->apiInstance->oauthAuthorize($appId,$state,$redirectUri);
        });
    }


    public function accessToken(array $params = []): array
    {

        return $this->handleMiddleware('token', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            $appId = $params['app_id'] ?? null;
            $secret = $params['secret'] ?? null;
            $authCode = $params['auth_code'] ?? null;

            return $this->apiInstance->accessToken($appId,$secret,$authCode);
        });
    }



    public function refreshToken(array $params = [])
    {

        return $this->handleMiddleware('token', $params, function(MiddlewareRequest $request) {

            $params = $request->getApiMethodParams();
            $appId = $params['app_id'] ?? null;
            $secret = $params['secret'] ?? null;
            $refreshToken = $params['refresh_token'] ?? null;

            return $this->apiInstance->refreshToken($appId,$secret,$refreshToken);
        });
    }



}
