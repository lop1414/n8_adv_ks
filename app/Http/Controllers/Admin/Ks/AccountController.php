<?php

namespace App\Http\Controllers\Admin\Ks;

use App\Common\Enums\StatusEnum;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Tools\CustomException;
use App\Models\BaiDu\BaiDuAccountModel;
use App\Models\Ks\KsAccountModel;
use Illuminate\Http\Request;

class AccountController extends KsController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new KsAccountModel();

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
                $item->admin_name = $this->adminMap[$item->admin_id]['name'];
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
                $builder->whereRaw("(account_id LIKE '%{$keyword}%' OR name LIKE '%{$keyword}%')");
            }

            //$builder->where('parent_id', '<>', 0);
        });
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->curdService->addField('name')->addValidRule('required|max:16|min:2');
        $this->curdService->addField('admin_id')->addValidRule('required');

        $this->curdService->saveBefore(function (){
            $this->model->existWithoutSelf('name',$this->curdService->handleData['name'],$this->curdService->handleData['id']);

            // 验证admin id
            $adminInfo = (new CenterApiService())->apiReadAdminUser($this->curdService->handleData['admin_id']);
            if($adminInfo['status'] != StatusEnum::ENABLE){
                throw new CustomException([
                    'code' => 'ADMIN_DISABLE',
                    'message' => '该后台用户已被禁用'
                ]);
            }
        });

        // 限制修改的字段
        $this->curdService->handleAfter(function (){
            foreach($this->curdService->handleData as $field => $val){
                if(!in_array($field,['name','admin_id','id'])){
                    unset($this->curdService->handleData[$field]);
                }
            }
        });
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 批量更新管理员
     */
    public function batchUpdateAdmin(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'admin_id'    =>  'required',
            'account_ids' =>  'required'
        ]);

        foreach ($req['account_ids'] as $accountId){
            $account = (new KsAccountModel)
                ->where('account_id',$accountId)
                ->first();
            if(empty($account)) continue;
            $account->admin_id = $req['admin_id'];
            $account->save();
        }

        return $this->success();
    }
}
