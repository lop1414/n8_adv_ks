<?php

namespace App\Console\Commands\SecondVersion;

use App\Common\Console\BaseCommand;
use App\Common\Enums\AdvAccountBelongTypeEnum;
use App\Models\Ks\KsAccountModel;
use App\Services\Ks\KsService;
use App\Services\SecondVersionService;

class ReloadKsAccountCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'second_version:reload_ks_account';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步二版快手账户';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 处理
     */
    public function handle(){
        $this->lockRun(
            [$this, '_run'],
            'second_version_reload_ks_account',
            7200,
            ['log' => true]
        );
    }

    /**
     * @return bool
     * @throws \App\Common\Tools\CustomException
     * 执行
     */
    protected function _run(){
        $ksAccountModel = new KsAccountModel();
        $ksAccounts = $ksAccountModel->where('belong_platform', AdvAccountBelongTypeEnum::SECOND_VERSION)
            ->enable()
            ->get();

        $ksService = new KsService();
        foreach($ksAccounts as $ksAccount){
            if($ksService->isFailAccessToken($ksAccount)){
                $secondVersionService = new SecondVersionService();
                $secondVersionService->syncKsAccount();
                break;
            }
        }

        return true;
    }
}
