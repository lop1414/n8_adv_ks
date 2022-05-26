<?php

namespace App\Services\Ks;

use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsAccountModel;
use App\Models\Ks\KsUserModel;
use App\Sdks\Ks\Ks;

class KsService extends BaseService
{
    /**
     * @var
     */
    public $sdk;

    /**
     * constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct();

        $this->sdk = new Ks();

        if(!empty($appId)){
            $this->setAppId($appId);
        }
    }

    /**
     * @param $appId
     * @return bool
     * 设置应用id
     */
    public function setAppId($appId){
        return $this->sdk->setAppId($appId);
    }

    /**
     * @return mixed
     * 获取应用id
     */
    public function getAppId(){
        return $this->sdk->getAppId();
    }

    /**
     * @param $accountId
     * @return bool
     * 设置账户id
     */
    public function setAccountId($accountId){
        return $this->sdk->setAccountId($accountId);
    }

    /**
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 获取账户id
     */
    public function getAccountId(){
        return $this->sdk->getAccountId();
    }

    /**
     * @throws CustomException
     * 设置 access_token (请求前必须调用)
     */
    protected function setAccessToken(){
        $accountId = $this->getAccountId();

        // 获取账户信息
        $ksAccountModel = new KsAccountModel();
        $skAccount = $ksAccountModel->where('app_id', $this->sdk->getAppId())
            ->where('account_id', $accountId)
            ->first();

        if(empty($skAccount)){
            throw new CustomException([
                'code' => 'NOT_FOUND_KS_ACCOUNT',
                'message' => "找不到该快手账户{{$accountId}}",
            ]);
        }


        // 设置token
        $this->sdk->setAccessToken($skAccount->access_token);
    }

    /**
     * @param $ksAccount
     * @return bool
     * access_token是否已失效
     */
    public function isFailAccessToken($ksAccount){
        $datetime = date('Y-m-d H:i:s', time());
        return $datetime > $ksAccount->fail_at;
    }

    /**
     * @param $accountId
     * @return mixed
     * @throws CustomException
     * 获取账户信息
     */
    public function getAccountInfo($accountId){
        $this->setAccessToken();
        return $this->sdk->getAccountInfo($accountId);
    }

    /**
     * @param array $accountIds
     * @return array
     * 获取账号组
     */
    public function getAccountGroup(array $accountIds = []){
        $ksAccountModel = new KsAccountModel();
        $builder = $ksAccountModel->where('status', StatusEnum::ENABLE);

        if(!empty($accountIds)){
            $accountIdsStr = implode("','", $accountIds);
            $builder->whereRaw("account_id IN ('{$accountIdsStr}')");
        }

        $accounts = $builder->get()->toArray();

        $groupSize = 10;
        $group = array_chunk($accounts, $groupSize);

        return $group;
    }

    /**
     * @param $accounts
     * @param $page
     * @param $pageSize
     * @param array $param
     * @throws CustomException
     * sdk批量获取列表
     */
    public function sdkMultiGetList($accounts, $page, $pageSize, $param = []){
        throw new CustomException([
            'code' => 'PLEASE_WRITE_SDK_MULTI_GET_LIST_CODE',
            'message' => '请书写sdk批量获取列表代码',
        ]);
    }

    /**
     * @param $accounts
     * @param $pageSize
     * @param array $param
     * @return array
     * @throws CustomException
     * 并发获取分页列表
     */
    public function multiGetPageList($accounts, $pageSize, $param = []){
        // 账户映射
        $accountMap = array_column($accounts, null, 'account_id');

        // 账户第一页数据
        $res = $this->sdkMultiGetList($accounts, 1, $pageSize, $param);

        // 查询其他页数
        $more = [];
        foreach($res as $v){
            if(empty($v['req']['param'])){
                continue;
            }
            $reqParam = json_decode($v['req']['param'], true);

            $totalPage = 1;
            if(!empty($v['data']['total_count'])){
                $totalPage = ceil($v['data']['total_count'] / $pageSize);
            }
            $advertiserId = $reqParam['advertiser_id'] ?? 0;

            if($advertiserId > 0 && $totalPage > 1){
                for($i = 2; $i <= $totalPage; $i++){
                    $more[$i][] = $accountMap[$advertiserId];
                }
            }
        }

        // 多页数据
        foreach($more as $page => $accounts){
            $tmp = $this->sdkMultiGetList($accounts, $page, $pageSize, $param);
            $res = array_merge($res, $tmp);
        }

        // 后置处理
        $res = $this->multiGetPageListAfter($res);

        // 数据过滤
        $list = [];
        foreach($res as $v){
            if(empty($v['data']['details']) || empty($v['req']['param'])){
                continue;
            }
            $reqParam = json_decode($v['req']['param'], true);

            foreach($v['data']['details'] as $item){
                $item['advertiser_id'] = $reqParam['advertiser_id'];
                $item['account_id'] = $reqParam['advertiser_id'];
                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * @param $res
     * @return mixed
     * 并发获取分页列表后置处理
     */
    public function multiGetPageListAfter($res){
        return $res;
    }


    /**
     * @param $userId
     * @return mixed
     * @throws CustomException
     * 获取快手用户
     */
    public function getKsUser($userId){
        $ksUserModel = new KsUserModel();
        $user = $ksUserModel
            ->where('id', $userId)
            ->first();

        if(empty($user)){
            throw new CustomException([
                'code' => 'NOT_FOUND_USER',
                'message' => '找不到对应用户,请确认用户是否正确',
                'data' => [
                    'user_id' => $userId,
                ],
            ]);
        }
        return $user;
    }
}
