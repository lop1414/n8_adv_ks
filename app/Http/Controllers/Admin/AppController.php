<?php

namespace App\Http\Controllers\Admin;

use App\Common\Controllers\Admin\AdminController;
use App\Common\Enums\StatusEnum;
use App\Common\Tools\CustomException;
use App\Models\AppModel;

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
