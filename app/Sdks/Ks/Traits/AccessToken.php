<?php

namespace App\Sdks\Ks\Traits;

use App\Common\Tools\CustomException;

trait AccessToken
{
    /**
     * @var
     * access token
     */
    protected $accessToken;

    /**
     * @param $accessToken
     * @return bool
     * 设置 access token
     */
    public function setAccessToken($accessToken){
        $this->accessToken = $accessToken;
        return true;
    }

    /**
     * @return mixed
     * @throws CustomException
     * 获取 access token
     */
    public function getAccessToken(){
        if(is_null($this->accessToken)){
            throw new CustomException([
                'code' => 'NOT_FOUND_ACCESS_TOKEN',
                'message' => '尚未设置access_token',
                'log' => true,
            ]);
        }
        return $this->accessToken;
    }


    public function getOauthAccessToken($secret,$authCode){
        $url = $this->getUrl('/oauth2/authorize/access_token');

        $param = [
            'app_id'    => $this->getAppId(),
            'secret'    => $secret,
            'auth_code' => $authCode,
        ];

        return $this->authRequest($url, $param, 'POST');
    }


    public function refreshAccessToken($appId,$secret,$refresh_token){
        $url = $this->getUrl('/oauth2/authorize/refresh_token');

        $this->setAccessToken('');
        $param = [
            'app_id'    => $appId,
            'secret'    => $secret,
            'refresh_token' => $refresh_token,
        ];

        return $this->authRequest($url, $param, 'POST');
    }
}
