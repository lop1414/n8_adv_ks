<?php

namespace App\Http\Controllers\Admin\Ks;


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

            //$builder->where('parent_id', '<>', 0);
        });
    }
}
