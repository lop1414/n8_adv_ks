<?php

namespace App\Http\Controllers\Front\Ks;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\ExceptionTypeEnum;
use App\Common\Services\ErrorLogService;
use App\Models\Ks\KsCampaignModel;
use App\Models\Ks\KsUnitModel;
use App\Models\Ks\Report\KsCreativeReportModel;
use App\Services\Ks\KsAccountService;
use App\Services\Ks\KsUnitService;
use Illuminate\Http\Request;

class IndexController extends FrontController
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
     * 授权
     */
    public function grant(Request $request){
        $data = $request->all();
        $this->validRule($data, [
            'auth_code' => 'required',
            'state'  => 'required'
        ]);

        $errorLogService = new ErrorLogService();
        $errorLogService->create('KS_OAUTH_GRANT_LOG', '快手Oauth授权日志', $data, ExceptionTypeEnum::CUSTOM);

        list($appId,$userId) = explode('|',$data['state']);
        $authCode = $data['auth_code'];

        $ret = (new KsAccountService())->grant($appId,$authCode,$userId);

        (new KsAccountService())->refreshAccessToken();

        return $this->ret($ret);
    }

}
