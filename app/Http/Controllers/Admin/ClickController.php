<?php

namespace App\Http\Controllers\Admin;

use App\Common\Controllers\Admin\AdminController;
use App\Common\Models\ClickModel;
use App\Common\Tools\CustomException;
use App\Services\AdvConvertCallbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClickController extends AdminController
{
    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'click_at';

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ClickModel();

        parent::__construct();
    }

    /**
     * 列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function($builder){
                // 6小时内
                $datetime = date('Y-m-d H:i:s', strtotime("-6 hours"));
                $builder->where('click_at', '>', $datetime);
            });
        });
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 回传
     */
    public function callback(Request $request){

        $this->validRule($request->post(), [
            'event_type' => 'required',
            'account_id' => 'required'
        ]);

        $eventType = $request->post('event_type');

        $advConvertCallbackService = new AdvConvertCallbackService();
        $eventTypeMap = $advConvertCallbackService->getEventTypeMap();
        $eventTypes = array_values($eventTypeMap);
        if(!in_array($eventType, $eventTypes)){
            throw new CustomException([
                'code' => 'UNKNOWN_EVENT_TYPE',
                'message' => '非合法回传类型',
            ]);
        }


        // 24小时内
        $datetime = date('Y-m-d H:i:s', strtotime("-6 hours"));
        $click = (new ClickModel())
            ->leftJoin('ks_campaigns AS c','clicks.campaign_id','=','c.id')
            ->select(DB::raw('clicks.*'))
            ->where('c.account_id',$request->post('account_id'))
            ->where('clicks.click_at', '>', $datetime)
            ->orderBy('click_at')
            ->first();
        if(empty($click)){
            throw new CustomException([
                'code' => 'NOT_CLICK_DATA',
                'message' => '未找到点击数据',
            ]);
        }

        $ret = $advConvertCallbackService->runCallback($click, $eventType,time(),1);

        return $this->ret($ret);
    }
}
