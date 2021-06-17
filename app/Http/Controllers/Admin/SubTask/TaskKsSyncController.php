<?php

namespace App\Http\Controllers\Admin\SubTask;

use App\Models\Task\TaskKsSyncModel;
use Illuminate\Http\Request;

class TaskKsSyncController extends SubTaskKsController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new TaskKsSyncModel();

        parent::__construct();
    }
}
