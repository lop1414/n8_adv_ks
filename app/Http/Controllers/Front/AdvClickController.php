<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\ClickController;
use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\AdvClickSourceEnum;
use App\Common\Services\ErrorLogService;
use App\Services\AdvClickService;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AdvClickController extends ClickController
{
    public function __construct(){
        parent::__construct(AdvAliasEnum::KS);
    }


    /**
     * @param Request $request
     * @return false|string
     * @throws \App\Common\Tools\CustomException
     * 点击
     */
    public function gdt(Request $request){
        $data = $request->all();
        (new ErrorLogService())->create('0','广点通检测书籍',$data,'CUSTOM');
    }

    /**
     * @return false|string
     * 广告商响应
     */
    protected function advResponse(){
        return json_encode([
            'code' => 0,
            'message' => 'SUCCESS'
        ]);
    }
}
