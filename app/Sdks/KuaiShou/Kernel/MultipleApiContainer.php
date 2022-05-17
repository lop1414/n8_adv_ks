<?php

namespace App\Sdks\KuaiShou\Kernel;



use App\Sdks\KuaiShou\Middleware\HandleResponseMiddleware;

class MultipleApiContainer extends ApiContainer
{

    protected $skipMiddleware = [
        HandleResponseMiddleware::class
    ];
}
