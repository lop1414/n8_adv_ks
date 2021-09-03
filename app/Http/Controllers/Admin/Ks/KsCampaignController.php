<?php

namespace App\Http\Controllers\Admin\Ks;


use App\Models\Ks\KsCampaignModel;

class KsCampaignController extends KsController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new KsCampaignModel();

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

            //$builder->where('parent_id', '<>', 0);
        });
    }
}
