<?php

namespace App\Console\Commands\Ks;

use App\Common\Console\BaseCommand;
use App\Common\Tools\CustomException;
use App\Services\Ks\KsCampaignService;
use App\Services\Ks\KsCreativeService;
use App\Services\Ks\KsProgramCreativeService;
use App\Services\Ks\KsUnitService;

class KsSyncCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'ks:sync {--type=} {--date=} {--account_ids=} {--status=} {--multi_chunk_size=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步快手信息';

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
        $param = $this->option();

        if(empty($param['type'])){
            throw new CustomException([
                'code' => 'NO_TYPE_PARAM',
                'message' => 'type 必传',
            ]);
        }

        // 账户
        if(!empty($param['account_ids'])){
            $param['account_ids'] = explode(",", $param['account_ids']);
        }

        $service = $this->getServices($param['type']);

        $option = ['log' => true];
        $this->lockRun(
            [$service, 'sync'],
            'baidu|sync|'.$param['type'],
            3600 * 3,
            $option,
            $param
        );
    }



    public function getServices($type){
        switch ($type){
            case 'campaign':
                echo "同步广告计划\n";
                $service = new KsCampaignService();
                break;
            case 'unit':
                echo "同步广告组\n";
                $service = new KsUnitService();
                break;
            case 'creative':
                echo "同步创意\n";
                $service = new KsCreativeService();
                break;
            case 'program_creative':
                echo "同步程序化创意\n";
                $service = new KsProgramCreativeService();
                break;
            default:
                throw new CustomException([
                    'code' => 'TYPE_PARAM_INVALID',
                    'message' => 'type 无效',
                ]);
        }
       return $service;
    }
}
