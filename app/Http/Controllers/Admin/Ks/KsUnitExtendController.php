<?php

namespace App\Http\Controllers\Admin\Ks;

use App\Common\Enums\StatusEnum;
use App\Common\Models\ConvertCallbackStrategyGroupModel;
use App\Common\Tools\CustomException;
use App\Common\Models\ConvertCallbackStrategyModel;
use App\Models\Ks\KsUnitExtendModel;
use App\Models\Ks\KsUnitModel;
use Illuminate\Http\Request;

class KsUnitExtendController extends KsController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new KsUnitExtendModel();

        parent::__construct();
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 批量更新
     */
    public function batchUpdate(Request $request){
        $this->validRule($request->post(), [
            'unit_ids' => 'required|array',
            'convert_callback_strategy_id' => 'required',
            'convert_callback_strategy_group_id' => 'integer',
        ]);
        $unitIds = $request->post('unit_ids');
        $convertCallbackStrategyId = $request->post('convert_callback_strategy_id');
        $convertCallbackStrategyGroupId = $request->post('convert_callback_strategy_group_id', 0);

        if(empty($convertCallbackStrategyId) && empty($convertCallbackStrategyGroupId)){
            throw new CustomException([
                'code' => 'CONVERT_CALLBACK_STRATEGY_AND_STRATEGY_GROUP_IS_EMPTY',
                'message' => '请选择回传策略或回传策略组',
            ]);
        }
        if(!empty($convertCallbackStrategyId)){
            // 回传规则是否存在
            $convertCallbackStrategyModel = new ConvertCallbackStrategyModel();
            $strategy = $convertCallbackStrategyModel->find($convertCallbackStrategyId);
            if(empty($strategy)){
                throw new CustomException([
                    'code' => 'NOT_FOUND_CONCERT_CALLBACK_STRATEGY',
                    'message' => '找不到对应回传策略',
                ]);
            }

            if($strategy->status != StatusEnum::ENABLE){
                throw new CustomException([
                    'code' => 'CONCERT_CALLBACK_STRATEGY_IS_NOT_ENABLE',
                    'message' => '该回传策略已被禁用',
                ]);
            }
        }

        if(!empty($convertCallbackStrategyGroupId)){
            // 回传规则是否存在
            $convertCallbackStrategyGroupModel = new ConvertCallbackStrategyGroupModel();
            $strategyGroup = $convertCallbackStrategyGroupModel->find($convertCallbackStrategyGroupId);
            if(empty($strategyGroup)){
                throw new CustomException([
                    'code' => 'NOT_FOUND_CONCERT_CALLBACK_STRATEGY_GROUP',
                    'message' => '找不到对应回传策略组',
                ]);
            }

            if($strategyGroup->status != StatusEnum::ENABLE){
                throw new CustomException([
                    'code' => 'CONCERT_CALLBACK_STRATEGY_GROUP_IS_NOT_ENABLE',
                    'message' => '该回传策略组已被禁用',
                ]);
            }
        }

        $units = [];
        foreach($unitIds as $unitId){
            $unit = KsUnitModel::find($unitId);
            if(empty($unit)){
                throw new CustomException([
                    'code' => 'NOT_FOUND_AD',
                    'message' => "找不到该广告组{{$unitId}}",
                ]);
            }
            $units[] = $unit;
        }

        foreach($units as $unit){
            $ksUnitExtend = KsUnitExtendModel::find($unit->id);

            if(empty($ksUnitExtend)){
                $ksUnitExtend = new KsUnitExtendModel();
                $ksUnitExtend->unit_id = $unit->id;
            }

            $ksUnitExtend->convert_callback_strategy_id = $convertCallbackStrategyId;
            $ksUnitExtend->convert_callback_strategy_group_id = $convertCallbackStrategyGroupId;

            $ksUnitExtend->save();
        }

        return $this->success();
    }
}
