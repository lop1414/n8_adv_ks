<?php

namespace App\Sdks\Ks\Traits;

use App\Common\Tools\CustomException;

trait Account
{
    /**
     * @var
     * 账户id
     */
    protected $accountId;

    /**
     * @param $accountId
     * @return bool
     * 设置账户id
     */
    public function setAccountId($accountId){
        $this->accountId = $accountId;
        return true;
    }

    /**
     * @return mixed
     * @throws CustomException
     * 获取账户id
     */
    public function getAccountId(){
        if(is_null($this->accountId)){
            throw new CustomException([
                'code' => 'NOT_FOUND_ACCOUNT_ID',
                'message' => '尚未设置账户id',
            ]);
        }
        return $this->accountId;
    }

    /**
     * @param $accountId
     * @return mixed
     * 获取账户信息
     */
    public function getAccountInfo($accountId){
        $url = $this->getUrl('/v1/advertiser/info');

        $param = [
            'advertiser_id' => $accountId
        ];

        return $this->authRequest($url, $param, 'GET');
    }


    public function getAccountList($userId,$token){
        $url = $this->getUrl('/gw/uc/v1/advertisers');

        $this->setAccessToken($token);
        $param = [
            'advertiser_id' => $userId
        ];

        return $this->authRequest($url, $param, 'POST');
    }
}
