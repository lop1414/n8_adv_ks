<?php

namespace App\Enums;

use App\Common\Enums\SystemAliasEnum;
use App\Models\Task\TaskKsSyncModel;
use App\Models\Task\TaskKsVideoUploadModel;

class TaskTypeEnum
{
    const KS_SYNC = 'KS_SYNC';
    const KS_VIDEO_UPLOAD = 'KS_VIDEO_UPLOAD';

    /**
     * @var string
     * 名称
     */
    static public $name = '任务类型';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        [
            'id' => self::KS_SYNC,
            'name' => '快手同步',
            'sub_model_class' => TaskKsSyncModel::class,
            'system_alias' => SystemAliasEnum::ADV_KS,
        ],
        [
            'id' => self::KS_VIDEO_UPLOAD,
            'name' => '快手视频上传',
            'sub_model_class' => TaskKsVideoUploadModel::class,
            'system_alias' => SystemAliasEnum::ADV_KS,
        ],
    ];
}
