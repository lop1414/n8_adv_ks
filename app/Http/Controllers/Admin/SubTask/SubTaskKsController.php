<?php

namespace App\Http\Controllers\Admin\SubTask;

use App\Common\Controllers\Admin\SubTaskController;
use App\Sdks\Ks\Ks;

class SubTaskKsController extends SubTaskController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 列表预处理
     */
    public function selectPrepare(){
        parent::selectPrepare();

        $this->curdService->selectQueryAfter(function(){
            foreach($this->curdService->responseData['list'] as $item){
                // 关联快手账户
                $item->ks_account;
            }
        });
    }

    /**
     * 详情预处理
     */
    public function readPrepare(){
        parent::readPrepare();
        $this->curdService->findAfter(function(){
            // 关联快手账户
            $this->curdService->findData->ks_account;
        });
    }

    /**
     * @param $failData
     * @return string
     * 获取失败原因
     */
    public function getFailReason($failData){
        if(empty($failData)){
            return '';
        }

        $code = $failData['code'] ?? '';
        $message = $failData['message'] ?? '';

        $sdk = new Ks();
        $map = $sdk->getCodeMessageMap();

        if(isset($map[$code])){
            $failReason = $map[$code];
        }else{
            $failReason = $message;
        }

        return $failReason;
    }
}
