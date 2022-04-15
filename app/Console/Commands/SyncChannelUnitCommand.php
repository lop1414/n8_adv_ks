<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsCreativeModel;
use App\Models\Ks\Report\KsCreativeReportModel;
use App\Services\ChannelUnitService;

class SyncChannelUnitCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'sync_channel_unit  {--date=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步渠道广告组关联';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @throws \App\Common\Tools\CustomException
     * 处理
     */
    public function handle(){
        do{
            $list = (new KsCreativeReportModel())->where('photo_id','')->limit(1000)->get();
            foreach ($list as $item){
                $item->photo_id = $item->extends->photo_id;
                $item->save();
            }

        }while(!$list->isEmpty());



die;
        $param = $this->option();

        $lockKey = 'sync_channel_unit_'. $param['date'];

        $option = ['log' => true];
        $this->lockRun(
            [$this, 'exec'],
            $lockKey,
            43200,
            $option,
            $param
        );
    }

    /**
     * @param $param
     * @return bool
     * @throws CustomException
     * 执行
     */
    public function exec($param){
        // 获取日期范围
        $dateRange = Functions::getDateRange($param['date']);
        $dateList = Functions::getDateListByRange($dateRange);

        $channelAdService = new ChannelUnitService();
        foreach($dateList as $date){
            $channelAdService->sync([
                'date' => $date,
            ]);
        }

        return true;
    }



}
