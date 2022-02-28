<?php

namespace App\Services;

use App\Common\Enums\ConvertTypeEnum;
use App\Common\Tools\CustomException;
use App\Common\Services\ConvertCallbackService;

class AdvConvertCallbackService extends ConvertCallbackService
{
    /**
     * @param $item
     * @return bool
     * @throws CustomException
     * 回传
     */
    protected function callback($item){
        $eventTypeMap = $this->getEventTypeMap();

        if(!isset($eventTypeMap[$item->convert_type])){
            // 无映射
            throw new CustomException([
                'code' => 'UNDEFINED_EVENT_TYPE_MAP',
                'message' => '未定义的事件类型映射',
                'log' => true,
                'data' => [
                    'item' => $item,
                ],
            ]);
        }

        // 关联点击
        if(empty($item->click)){
            throw new CustomException([
                'code' => 'NOT_FOUND_CONVERT_CLICK',
                'message' => '找不到该转化对应点击',
                'log' => true,
                'data' => [
                    'item' => $item,
                ],
            ]);
        }

        $eventType = $eventTypeMap[$item->convert_type];

        //付费金额
        $payAmount = 0;
        if(!empty($item->extends->convert->amount)){
            $payAmount =  $item->extends->convert->amount;
        }

        $eventTime = strtotime($item->convert_at);
        $this->runCallback($item->click,$eventType,$eventTime,$payAmount);

        return true;
    }



    public function runCallback($click,$eventType,$eventTime,$payAmount = 0){
        $param = [
            'event_type' => $eventType,
            'event_time' => $eventTime
        ];
        if(!empty($payAmount)){
            $param['purchase_amount'] = $payAmount;
        }

        if(!empty($click->link)){
            $param['link'] = $click->link;
            $url = 'http://ad.partner.gifshow.com/track/activate/'.'?'. http_build_query($param);

        }else{
            $url = $click->callback. '&' . http_build_query($param);
        }


        $ret = file_get_contents($url);
        $result = json_decode($ret, true);

        if(!isset($result['result']) || $result['result'] != 1){
            throw new CustomException([
                'code' => 'KS_CONVERT_CALLBACK_ERROR',
                'message' => '快手转化回传失败',
                'log' => true,
                'data' => [
                    'url' => $url,
                    'param' => $param,
                    'result' => $result,
                ],
            ]);
        }

        return true;

    }



    /**
     * @return array
     * 获取事件映射
     */
    public function getEventTypeMap(){
        return [
            ConvertTypeEnum::ACTIVATION => 1,
            ConvertTypeEnum::REGISTER => 1,
            ConvertTypeEnum::FOLLOW => 1,
            ConvertTypeEnum::ADD_DESKTOP => 1,
            ConvertTypeEnum::PAY => 3,
        ];
    }



    /**
     * @param $click
     * @return array|void
     */
    public function filterClickData($click){
        return [
            'id' => $click['id'],
            'campaign_id' => $click['campaign_id'],
            'ad_id' => $click['unit_id'],
            'creative_id' => $click['creative_id'],
            'click_at' => $click['click_at'],
        ];
    }
}
