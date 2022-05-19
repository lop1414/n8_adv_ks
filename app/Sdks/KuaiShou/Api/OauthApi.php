<?php

namespace App\Sdks\KuaiShou\Api;

use App\Sdks\KuaiShou\Kernel\Api;
use GuzzleHttp\Psr7\Request;

/**
 * Class OauthApi
 * @package App\Sdks\KuaiShou\Api
 */
class OauthApi extends Api
{

    public function oauthAuthorize(int $appId,string $state,string $redirectUri): string
    {
        $uri = 'https://developers.e.kuaishou.com/tools/authorize?';
        $uri .= http_build_query([
            'app_id' => $appId,
            'scope' => '["ad_query","ad_manage","public_dmp_service","report_service","public_agent_service","public_account_service","account_service"]',
            'state' => $state,
            'redirect_uri' => $redirectUri
        ]);

        return $uri;
    }



    public function token(int $appId,string $secret,string $authCode){
        $request = $this->tokenRequest($appId,$secret,$authCode);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }



    protected function tokenRequest(int $appId,string $secret,string $authCode): Request
    {
        $resourcePath = '/oauth2/authorize/access_token';
        $queryParams = [
            'app_id' => $appId,
            'secret' => $secret,
            'auth_code' => $authCode
        ];

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParams);
        return new Request('POST', $uri,$headers,$httpBody);
    }



    public function refreshToken(int $appId,string $secret,string $refreshToken){
        $request = $this->refreshTokenRequest($appId,$secret,$refreshToken);
        $response = $this->client->send($request);
        return $this->handleResponse($response);
    }


    protected function refreshTokenRequest(int $appId,string $secret,string $refreshToken): Request
    {
        $resourcePath = '/oauth2/authorize/refresh_token';
        $queryParams = [
            'app_id'    => $appId,
            'secret'    => $secret,
            'refresh_token'    => $refreshToken,
        ];

        $uri = $this->config->getHost() . $resourcePath;
        $headers = [];
        $httpBody = json_encode($queryParams);
        return new Request('POST', $uri,$headers,$httpBody);
    }


}
