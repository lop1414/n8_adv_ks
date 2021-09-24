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

        $ret = (new KsAccountService($appId))->grant($authCode,$userId);

        (new KsAccountService())->refreshAccessToken();

        return $this->ret($ret);
    }


    /**
     * @param Request $request
     * @throws \App\Common\Tools\CustomException
     * 接收二版上报的广告组
     */
    public function unit(Request $request){
        $data = $request->all();

        $this->validRule($data, [
            'unit_id'  => 'required',
        ]);
        (new KsUnitService())->save($data);
    }


    /**
     * @param Request $request
     * @return bool
     * @throws \App\Common\Tools\CustomException
     * 接收二版上报的创意报表
     */
    public function creativeReport(Request $request){
        $data = $request->all();
        $this->validRule($data, [
            'stat_datetime'=> 'required',
            'creative_id'  => 'required',
            'unit_id'  => 'required',
        ]);

        $info = (new KsCreativeReportModel())
            ->where('stat_datetime',$data['stat_datetime'])
            ->where('creative_id',$data['creative_id'])
            ->first();
        if(empty($info)){
            $info = new KsCreativeReportModel();
            $info->stat_datetime = $data['stat_datetime'];
            $info->creative_id = $data['creative_id'];
            $info->unit_id = $data['unit_id'];

            $unit = (new KsUnitModel())->where('id',$data['unit_id'])->first();

            $info->account_id = $unit->account_id;
            $info->campaign_id = $unit->campaign_id;
        }

        $info->charge = $data['charge'];
        $info->show   = $data['show'];
        $info->photo_click = $data['photo_click'];
        $info->aclick = $data['aclick'];
        $info->bclick = $data['bclick'];

        $info->extends = ['source' => 'SECOND_VERSION'];

        $info->save();
    }

}
