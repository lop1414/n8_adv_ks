<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\ChannelUnitService;
use Illuminate\Http\Request;

class ChannelUnitController extends FrontController
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 批量更新
     */
    public function batchUpdate(Request $request){
        $data = $request->post();

        $channelUnitService = new ChannelUnitService();
        $ret = $channelUnitService->batchUpdate($data);

        return $this->ret($ret);
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 列表
     */
    public function select(Request $request){
        $param = $request->post();

        $channelUnitService = new ChannelUnitService();
        $data = $channelUnitService->select($param);

        return $this->success($data);
    }
}
