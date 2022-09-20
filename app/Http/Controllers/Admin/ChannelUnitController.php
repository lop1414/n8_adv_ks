<?php

namespace App\Http\Controllers\Admin;

use App\Common\Controllers\Admin\AdminController;
use App\Services\ChannelUnitService;
use Illuminate\Http\Request;

class ChannelUnitController extends AdminController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 详情
     */
    public function read(Request $request){
        $data = $request->post();

        $channelUnitService = new ChannelUnitService();
        $data = $channelUnitService->read($data);

        return $this->success($data);
    }
}
