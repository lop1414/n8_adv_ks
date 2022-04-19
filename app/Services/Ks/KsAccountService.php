<?php

namespace App\Services\Ks;

use App\Common\Enums\AdvAccountBelongTypeEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Enums\Ks\KsSyncTypeEnum;
use App\Models\AppModel;
use App\Models\Ks\KsAccountModel;
use App\Models\Ks\KsUserModel;
use App\Services\Task\TaskKsSyncService;

class KsAccountService extends KsService
{
    /**
     * OceanVideoService constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);
    }


    /**
     * @param $appId
     * @param $user
     * @throws CustomException
     * 验证用户是否为绑定的app
     */
    public function checkAppBelongToUser($appId,$user){
        if($appId != $user->app_id){
            throw new CustomException([
                'code' => 'APP_NOT_BELONG_TO_USER',
                'message' => '应用程序不属于用户',
                'data' => [
                    'app_id' => $appId,
                    'user_id' => $user
                ],
            ]);
        }
    }



    /**
     * @param $authCode
     * @param $userId
     * @return bool
     * @throws CustomException
     * 授权
     */
    public function grant($authCode,$userId){
        $appId = $this->sdk->getAppId();

        $appModel = new AppModel();
        $app = $appModel->where('app_id', $appId)->first();
        if(empty($app)){
            throw new CustomException([
                'code' => 'NOT_FOUND_APP',
                'message' => '找不到对应app',
                'data' => [
                    'app_id' => $appId,
                ],
            ]);
        }

        $user = $this->getKsUser($userId);
        $this->checkAppBelongToUser($appId,$user);

        if(!Functions::isLocal()){
            $this->sdk->setAccessToken('');
            $info = $this->sdk->getOauthAccessToken($app->secret, $authCode);
            var_dump($info);
        }else{
            $info = [
                'user_id'       => $userId,
                'advertiser_id' => '',
                'access_token'  => '111',
                'refresh_token' => '222',
                'access_token_expires_in' => 86399,
            ];
        }

        $user->access_token = $info['access_token'];
        $user->refresh_token = $info['refresh_token'];
        $user->fail_at = date('Y-m-d H:i:s', time() + $info['access_token_expires_in'] - 2000);
        $user->save();

        // 创建任务
        $taskSyncService = new TaskKsSyncService(KsSyncTypeEnum::ACCOUNT);
        $task = [
            'name' => "快手账户同步",
            'admin_id' => 0,
        ];

        $subs = [];
        $subs[] = [
            'app_id' => $appId,
            'account_id' => 0,
            'admin_id' => $task['admin_id'],
            'extends' =>  ['user_id' => $userId]

        ];
        $taskSyncService->create($task, $subs);

        return true;
    }


    /**
     * @return bool
     * @throws CustomException
     * 批量同步
     */
    public function batchSync(){
        $ksUserModel = new KsUserModel();
        $ksUsers = $ksUserModel->get();


        // 创建任务
        $taskKsSyncService = new TaskKsSyncService(KsSyncTypeEnum::ACCOUNT);
        $task = [
            'name' => "快手账户同步",
            'admin_id' => 0,
        ];
        $subs = [];
        foreach($ksUsers as $ksUser){
            $subs[] = [
                'app_id'    => $ksUser->app_id,
                'account_id'=> 0,
                'admin_id' => $task['admin_id'],
                'extends' =>  ['user_id' => $ksUser->id]
            ];
        }

        $taskKsSyncService->create($task, $subs);

        return true;
    }


    /**
     * @param $option
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($option){
        $KsUser = $this->getKsUser($option['user_id']);
        $accounts = $this->sdk->getAccountList($option['user_id'],$KsUser->access_token);

        foreach($accounts['details'] as $account){
            $accountInfo = $this->sdk->getAccountInfo($account['advertiser_id']);

            $ksAccount = (new KsAccountModel())
                ->where('app_id',$KsUser['app_id'])
                ->where('account_id',$account['advertiser_id'])
                ->get();

            if(empty($ksAccount)){
                $ksAccount = new KsAccountModel();
                $ksAccount->app_id = $KsUser['app_id'];
                $ksAccount->account_id = $account['advertiser_id'];
                $ksAccount->parent_id = 0;
                $ksAccount->admin_id = 0;
                $ksAccount->user_id = $accountInfo['user_id'];
                $ksAccount->status = StatusEnum::ENABLE;
                $ksAccount->belong_platform = AdvAccountBelongTypeEnum::LOCAL;

            }elseif($ksAccount->user_id != $accountInfo['user_id']){
                continue;
            }

            $ksAccount->name = $account['advertiser_name'];
            $ksAccount->company = $accountInfo['corporation_name'];
            $ksAccount->extend = [];
            $ksAccount->access_token = $KsUser->access_token;
            $ksAccount->refresh_token = '';
            $ksAccount->fail_at = $KsUser->fail_at;
            $ksAccount->save();

        }
        return true;
    }



    /**
     * @return bool
     * 刷新 access token
     */
    public function refreshAccessToken(){
        $ksUsers = (new KsUserModel())->get();


        foreach($ksUsers as $user){
            $app = (new AppModel())->where('app_id',$user->app_id)->first();
            if(!Functions::isLocal()){
                $info = $this->sdk->refreshAccessToken($user->app_id, $app->secret, $user->refresh_token);
                var_dump($info);
            }else{
                $info = [
                    'user_id'       => $user->id,
                    'advertiser_id' => '',
                    'access_token' => '111',
                    'refresh_token' => '222',
                    'access_token_expires_in' => 86399,
                ];
            }

            $user->access_token = $info['access_token'];
            $user->refresh_token = $info['refresh_token'];
            $user->fail_at = date('Y-m-d H:i:s', time() + $info['access_token_expires_in'] - 2000);
            $user->save();


            $ksAccounts = (new KsAccountModel())->where('app_id',$app->app_id)->get();
            foreach ($ksAccounts as $ksAccount){
                $ksAccount->access_token = $user['access_token'];
                $ksAccount->refresh_token = '';
                $ksAccount->fail_at = $user->fail_at;
                $ksAccount->save();
            }
        }

        return true;
    }
}
