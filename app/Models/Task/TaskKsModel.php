<?php

namespace App\Models\Task;

use App\Common\Models\SubTaskModel;

class TaskKsModel extends SubTaskModel
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * 关联快手账户模型 一对一
     */
    public function ks_account(){
        return $this->belongsTo('App\Models\Ks\KsAccountModel', 'account_id', 'account_id');
    }
}
