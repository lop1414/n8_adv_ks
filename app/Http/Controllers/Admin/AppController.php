<?php

namespace App\Http\Controllers\Admin;

use App\Common\Controllers\Admin\AdminController;
use App\Common\Enums\StatusEnum;
use App\Common\Enums\SystemAliasEnum;
use App\Common\Tools\CustomException;
use App\Models\AppModel;
use App\Models\Ks\KsUserModel;

class AppController extends AdminController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new AppModel();

        parent::__construct();
    }

    public function getAuthUrl($appId,$userId){
        $redirectUri = config('common.system_api.'.SystemAliasEnum::ADV_KS.'.url').'/front/ks/grant';

        $url = 'https://developers.e.kuaishou.com/tools/authorize?';
        $url .= http_build_query([
            'app_id' => $appId,
            'scope' => '["ad_query","ad_manage","public_dmp_service","report_service","public_agent_service","public_account_service","account_service"]',
            'state' => $appId.'|'.$userId,
            'redirect_uri' => $redirectUri
        ]);
        return $url;
    }


    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        parent::selectPrepare();

        $this->curdService->selectQueryAfter(function(){
            foreach ($this->curdService->responseData['list'] as $item){
                $user = (new KsUserModel())->where('app_id',$item->app_id)->first();
                if(empty($user)) continue;

                $item->auth_url = $this->getAuthUrl($item['app_id'],$user->id);
            }
        });
    }


    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->curdService->addField('app_id')->addValidRule('required');
        $this->curdService->addField('secret')->addValidRule('required');
        $this->curdService->saveBefore(function(){
            if($this->curdService->getModel()->uniqueExist([
                'app_id' => $this->curdService->handleData['app_id']
            ])){
                throw new CustomException([
                    'code' => 'DATA_EXIST',
                    'message' => 'app id 已存在'
                ]);
            }

            if(empty($this->curdService->handleData['status'])){
                $this->curdService->handleData['status'] = StatusEnum::ENABLE;
            }
        });
    }


    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->curdService->saveBefore(function (){
            $this->model->existWithoutSelf('name',$this->curdService->handleData['name'],$this->curdService->handleData['id']);
            $this->model->existWithoutSelf('app_id',$this->curdService->handleData['app_id'],$this->curdService->handleData['id']);
        });
    }
}
