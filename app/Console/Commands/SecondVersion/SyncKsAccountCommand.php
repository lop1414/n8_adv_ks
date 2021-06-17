<?php

namespace App\Console\Commands\SecondVersion;

use App\Common\Console\BaseCommand;
use App\Services\SecondVersionService;

class SyncKsAccountCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'second_version:sync_ks_account';

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
        $secondVersionService = new SecondVersionService();
        $secondVersionService->syncKsAccount(['is_sync_account_info' => true]);
    }
}
