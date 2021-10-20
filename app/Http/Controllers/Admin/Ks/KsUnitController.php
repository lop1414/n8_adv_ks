<?php

namespace App\Http\Controllers\Admin\Ks;


use App\Common\Models\ConvertCallbackStrategyModel;
use App\Models\Ks\KsUnitModel;

class KsUnitController extends KsController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new KsUnitModel();
        parent::__construct();
    }

    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        parent::selectPrepare();

        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function($builder){
                $this->filter();
            });
        });

        $this->curdService->selectQueryAfter(function(){

            foreach ($this->curdService->responseData['list'] as $item){
                $item->ks_account;
                $item->campaign;
                $item->channel_unit;
                if(!empty($item->ks_unit_extends)){
                    $item->convert_callback_strategy = ConvertCallbackStrategyModel::find($item->ks_unit_extends->convert_callback_strategy_id);
                }else{
                    $item->convert_callback_strategy = null;
                }
                $item->admin_name = $this->adminMap[$item->ks_account->admin_id]['name'];
            }
        });
    }

    /**
     * 列表预处理
     */
    public function getPrepare(){
        parent::getPrepare();

        $this->curdService->getQueryBefore(function(){
            $this->filter();
        });

        $this->curdService->getQueryAfter(function(){
            foreach ($this->curdService->responseData as $item){
                $item->ks_account;
            }
        });
    }

    /**
     * 过滤
     */
    private function filter(){
        $this->curdService->customBuilder(function($builder){
            // 关键词
            $keyword = $this->curdService->requestData['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw("(id LIKE '%{$keyword}%' OR name LIKE '%{$keyword}%')");
            }

            // 筛选渠道
            if(isset($this->curdService->requestData['channel_id'])){
                $channelId = $this->curdService->requestData['channel_id'];
                $builder->whereRaw("id IN (
                SELECT unit_id FROM channel_units
                    WHERE channel_id = {$channelId}
                )");
            }

            //$builder->where('parent_id', '<>', 0);
        });
    }
}
