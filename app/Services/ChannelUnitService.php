<?php

namespace App\Services;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Helpers\Advs;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Services\SystemApi\UnionApiService;
use App\Common\Tools\CustomException;
use App\Models\ChannelUnitLogModel;
use App\Models\ChannelUnitModel;
use App\Models\Ks\KsAccountModel;
use App\Models\Ks\KsCreativeModel;
use App\Models\Ks\KsProgramCreativeModel;
use App\Models\Ks\KsUnitModel;
use Illuminate\Support\Facades\DB;

class ChannelUnitService extends BaseService
{

    public $keyword;

    /**
     * @param $data
     * @return bool
     * @throws CustomException
     * 批量更新
     */
    public function batchUpdate($data){
        $this->validRule($data, [
            'channel_id' => 'required|integer',
            'unit_ids'   => 'required|array',
            'channel'    => 'required',
            'platform'   => 'required'
        ]);

        Functions::hasEnum(PlatformEnum::class, $data['platform']);

        DB::beginTransaction();

        try{
            foreach($data['unit_ids'] as $unitId){
                $this->update([
                    'unit_id' => $unitId,
                    'channel_id' => $data['channel_id'],
                    'platform' => $data['platform'],
                    'extends' => [
                        'channel' => $data['channel'],
                    ],
                ]);
            }
        }catch(CustomException $e){
            DB::rollBack();
            throw $e;
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return true;
    }

    /**
     * @param $data
     * @return bool
     * 更新
     */
    public function update($data){
        $channelUnitModel = new ChannelUnitModel();
        $channelUnit = $channelUnitModel
            ->where('unit_id', $data['unit_id'])
            ->where('platform', $data['platform'])
            ->first();

        $flag = $this->buildFlag($channelUnit);
        if(empty($channelUnit)){
            $channelUnit = new ChannelUnitModel();
        }

        $channelUnit->unit_id = $data['unit_id'];
        $channelUnit->channel_id = $data['channel_id'];
        $channelUnit->platform = $data['platform'];
        $channelUnit->extends = $data['extends'];
        $ret = $channelUnit->save();
        if($ret && !empty($channelUnit->unit_id) && $flag != $this->buildFlag($channelUnit)){
            $this->createChannelAdLog([
                'channel_unit_id' => $channelUnit->id,
                'unit_id'    => $data['unit_id'],
                'channel_id' => $data['channel_id'],
                'platform'   => $data['platform'],
                'extends'    => $data['extends']
            ]);
        }

        return $ret;
    }

    /**
     * @param $channelUnit
     * @return string
     * 构建标识
     */
    protected function buildFlag($channelUnit){
        $adminId = !empty($channelUnit->extends->channel->admin_id) ? $channelUnit->extends->channel->admin_id : 0;
        if(empty($channelUnit)){
            $flag = '';
        }else{
            $flag = implode("_", [
                $channelUnit->unit_id,
                $channelUnit->channel_id,
                $channelUnit->platform,
                $adminId
            ]);
        }
        return $flag;
    }

    /**
     * @param $data
     * @return bool
     * 创建渠道-计划日志
     */
    protected function createChannelAdLog($data){
        $channelUnitLogModel = new ChannelUnitLogModel();
        $channelUnitLogModel->channel_unit_id = $data['channel_unit_id'];
        $channelUnitLogModel->unit_id = $data['unit_id'];
        $channelUnitLogModel->channel_id = $data['channel_id'];
        $channelUnitLogModel->platform = $data['platform'];
        $channelUnitLogModel->extends = $data['extends'];
        return $channelUnitLogModel->save();
    }

    /**
     * @param $param
     * @return array
     * @throws CustomException
     * 列表
     */
    public function select($param){
        $this->validRule($param, [
            'start_datetime' => 'required',
            'end_datetime' => 'required',
        ]);
        Functions::timeCheck($param['start_datetime']);
        Functions::timeCheck($param['end_datetime']);
        $channelUnitModel = new ChannelUnitModel();
        $channelUnits = $channelUnitModel->whereBetween('updated_at', [$param['start_datetime'], $param['end_datetime']])->get();

        $distinct = $data = [];
        foreach($channelUnits as $channelUnit){
            if(empty($distinct[$channelUnit['channel_id']])){
                // 广告组
                $ksUnit = KsUnitModel::find($channelUnit['unit_id']);
                if(empty($ksUnit)){
                    continue;
                }

                // 账户
                $ksAccount = (new KsAccountModel())->where('account_id', $ksUnit['account_id'])->first();
                if(empty($baiduAccount)){
                    continue;
                }

                $data[] = [
                    'channel_id' => $channelUnit['channel_id'],
                    'ad_id' => $channelUnit['ad_id'],
                    'ad_name' => $ksUnit['name'],
                    'account_id' => $ksUnit['account_id'],
                    'account_name' => $ksAccount['name'],
                    'admin_id' => $ksAccount['admin_id'],
                ];
                $distinct[$channelUnit['channel_id']] = 1;
            }
        }

        return $data;
    }


    public function getKeyWord(){
        if(empty($this->keyword)){
            $this->keyword = 'sign='. Advs::getAdvClickSign(AdvAliasEnum::KS);
        }
        return $this->keyword;
    }




    /**
     * @param $param
     * @return bool
     * 同步
     */
    public function sync($param){
        $date = $param['date'];

        $startTime = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($date)));
        $endTime = "{$date} 23:59:59";



        $this->programCreative($startTime,$endTime);
        $this->creative($startTime,$endTime);

        return true;
    }


    public function programCreative($startTime,$endTime){
        $lastMaxId = 0;

        do{
            $ksCreatives = (new KsProgramCreativeModel())
                ->where('id','>',$lastMaxId)
                ->whereBetween('updated_at', [$startTime, $endTime])
                ->skip(0)
                ->take(1000)
                ->orderBy('id')
                ->get();


            foreach($ksCreatives as $ksCreative){
                $lastMaxId = $ksCreative['id'];

                $clickUrl = $ksCreative->extends->click_url ?? '';

                $this->updateChannelUnit($ksCreative->id,$clickUrl);
            }
        }while(!$ksCreatives->isEmpty());
    }



    public function creative($startTime,$endTime){
        $lastMaxId = 0;

        do{
            $ksCreatives = (new KsCreativeModel())
                ->where('id','>',$lastMaxId)
                ->whereBetween('updated_at', [$startTime, $endTime])
                ->skip(0)
                ->take(1000)
                ->orderBy('id')
                ->get();


            foreach($ksCreatives as $ksCreative){
                $lastMaxId = $ksCreative['id'];

                $clickTrackUrl = $ksCreative->extends->click_track_url ?? '';

                $this->updateChannelUnit($ksCreative->unit_id,$clickTrackUrl);

            }
        }while(!$ksCreatives->isEmpty());
    }



    public function updateChannelUnit($unitId,$clickUrl){

        if(empty($clickUrl)){
//            echo "click url 为空;";
            return;
        }

        $keyword = $this->getKeyWord();
        if(strpos($clickUrl, $keyword) === false){
//            var_dump($clickUrl);
            return;
        }


        $unionApiService = new UnionApiService();

        $ret = parse_url($clickUrl);
        parse_str($ret['query'], $param);

        if(!empty($param['android_channel_id'])){
            $channel = $unionApiService->apiReadChannel(['id' => $param['android_channel_id']]);
            $channelExtends = $channel['channel_extends'] ?? [];
            $channel['admin_id'] = $channelExtends['admin_id'] ?? 0;
            unset($channel['extends']);
            unset($channel['channel_extends']);

            $this->update([
                'unit_id' => $unitId,
                'channel_id' => $param['android_channel_id'],
                'platform' => PlatformEnum::DEFAULT,
                'extends' => [
                    'track_url' => $clickUrl,
                    'channel' => $channel,
                ],
            ]);
        }
    }


    /**
     * @param $data
     * @return array
     * @throws CustomException
     * 详情
     */
    public function read($data){
        $this->validRule($data, [
            'channel_id' => 'required|integer'
        ]);

        $channelUnitModel = new ChannelUnitModel();
        $unitIds = $channelUnitModel->where('channel_id', $data['channel_id'])->pluck('unit_id')->toArray();

        $builder = new KsUnitModel();
        $builder = $builder->whereIn('id', $unitIds);

        // 过滤
        if(!empty($data['filtering'])){
            $builder = $builder->filtering($data['filtering']);
        }

        $res = $builder->listPage($data['page'] ?? 1, $data['page_size'] ?? 10);


        $adminMap = (new CenterApiService())->getAdminUserMap();


        foreach($res['list'] as $unit){
            unset($unit->extends);

            $unit->ks_account;
            $unit->campaign;
            $unit->admin_name = $adminMap[$unit->ks_account->admin_id]['name'];
            if(!empty($unit->ks_unit_extends)){
                $unit->convert_callback_strategy = $unit->ks_unit_extends->convert_callback_strategy;
                $unit->convert_callback_strategy_group = $unit->ks_unit_extends->convert_callback_strategy_group;

                unset($unit->ks_unit_extends);
            }else{
                $unit->convert_callback_strategy = null;
                $unit->convert_callback_strategy_group = null;
            }
        }

        $res['channel_id'] = $data['channel_id'];
        return $res;
    }
}
