<?php

namespace App\Services;

use App\Common\Enums\AdvClickSourceEnum;
use App\Common\Helpers\Functions;
use App\Common\Models\ClickModel;
use App\Common\Services\ClickService;
use App\Common\Tools\CustomException;
use App\Enums\QueueEnums;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class AdvClickService extends ClickService
{
    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(QueueEnums::CLICK);
    }

    /**
     * @param $data
     * @return mixed
     * @throws CustomException
     * 数据过滤
     */
    public function dataFilter($data){
        // 验证
        $this->validRule($data, [
            'click_source' => 'required',
        ]);
        Functions::hasEnum(AdvClickSourceEnum::class, $data['click_source']);

        if(empty($data['imei'])) $data['imei'] = null;
        if(empty($data['idfa'])) $data['idfa'] = null;
        if(empty($data['muid'])) $data['muid'] = null;
        if(empty($data['android_id'])) $data['android_id'] = null;
        if(empty($data['oaid'])) $data['oaid'] = null;
        $muid = '';
        if(!empty($data['imei'])){
            $muid = trim($data['imei']);
        }elseif(!empty($data['idfa'])){
            $muid = trim($data['idfa']);
        }elseif(!empty($data['muid'])){
            $muid = trim($data['muid']);
        }
        $data['muid'] = $muid;


        $clickAt = null;
        if(!empty($data['click_at'])){
            if($data['click_at'] == '__TS__'){
                throw new CustomException([
                    'code' => 'CLICK_AT_IS_ERROR',
                    'message' => '点击时间格式错误-未替换宏参数',
                    'log' => false,
                    'data' => $data,
                ]);
            }

            if(!is_numeric($data['click_at'])){
                throw new CustomException([
                    'code' => 'CLICK_AT_IS_ERROR',
                    'message' => '点击时间格式错误',
                    'log' => true,
                    'data' => $data,
                ]);
            }

            $clickAt = date('Y-m-d H:i:s', intval($data['click_at'] / 1000));
            if(!Functions::timeCheck($clickAt)){
                throw new CustomException([
                    'code' => 'CLICK_AT_IS_ERROR',
                    'message' => '点击时间格式错误',
                    'log' => true,
                    'data' => $data,
                ]);
            }
        }

        if(empty($clickAt)){
            throw new CustomException([
                'code' => 'CLICK_AT_IS_NULL',
                'message' => '点击时间不能为空',
                'log' => true,
                'data' => $data
            ]);
        }

        if($data['click_source'] == AdvClickSourceEnum::ADV_CLICK_API){
            // 广告商api
            $data['os'] = $data['os'] ?? 3;
            if($data['os'] == 1 && !empty($data['ios_channel_id'])){
                // IOS
                $data['channel_id'] = $data['ios_channel_id'];
            }elseif($data['os'] == 0 && !empty($data['android_channel_id'])){
                // ANDROID
                $data['channel_id'] = $data['android_channel_id'];
            }else{
                $data['channel_id'] = 0;
            }
        }elseif($data['click_source'] == AdvClickSourceEnum::N8_AD_PAGE){
            // n8广告页
            $agent = new Agent();
            $agent->setUserAgent($data['ua']);

            if($agent->isIOS() && !empty($data['ios_channel_id'])){
                $data['channel_id'] = $data['ios_channel_id'];
            }elseif($agent->isAndroidOS() && !empty($data['android_channel_id'])){
                $data['channel_id'] = $data['android_channel_id'];
            }else{
                $data['channel_id'] = 0;
            }
        }

        if(!empty($data['link'])){
            if($data['link'] == base64_encode(base64_decode($data['link']))){
                $data['link'] = base64_decode($data['link']);
            }
        }

        $data['click_at'] = $clickAt;
        return $data;
    }

    /**
     * @param $data
     * @return bool|void
     * 创建
     */
    protected function create($data){
        $clickModel = new ClickModel();
        $clickModel->click_source = $data['click_source'] ?? '';
        $clickModel->campaign_id = empty($data['campaign_id']) ? 0: $data['campaign_id'];
        $clickModel->unit_id = empty($data['unit_id']) ? 0: $data['unit_id'];
        $clickModel->creative_id = empty($data['creative_id']) ? 0: $data['creative_id'];
        $clickModel->request_id = $data['request_id'] ?? '';
        $clickModel->channel_id = $data['channel_id'] ?? 0;
        $clickModel->muid = $data['muid'] ?? '';
        $clickModel->android_id = $data['android_id'] ?? '';
        $clickModel->oaid = $data['oaid'] ?? '';
        $clickModel->oaid_md5 = $data['oaid_md5'] ?? '';
        $clickModel->os = $data['os'] ?? '';
        $clickModel->ip = $data['ip'] ?? '';
        $clickModel->ua = $data['ua'] ?? '';
        $clickModel->click_at = $data['click_at'] ?? null;
        $clickModel->callback = $data['callback'] ?? '';
        $clickModel->link = $data['link'] ?? '';
        $clickModel->extends = $data['extends'] ?? [];
        $ret = $clickModel->save();

        return $ret;
    }

    /**
     * 清除无效点击
     * @param string $dateTime
     * @return array
     */
    public function cleanInvalidClick(string $dateTime): array
    {
        $i = 0;
        $clickModel = new ClickModel();
        do{
            $list = $clickModel
                ->where('click_at','<=',$dateTime)
                ->whereRaw("id NOT IN ( SELECT click_id FROM convert_callbacks)")
                ->limit(100)
                ->get();
            foreach ($list as $item){
                $item->delete();
                $i++;
            }
        }while(!$list->isEmpty());

        return [
            'del_count' => $i
        ];
    }
}
