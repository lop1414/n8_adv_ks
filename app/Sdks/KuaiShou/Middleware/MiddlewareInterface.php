<?php

namespace App\Sdks\KuaiShou\Middleware;

use App\Sdks\KuaiShou\Middleware\Model\MiddlewareRequest;
use Closure;


interface MiddlewareInterface
{
    /**
     * Handle middleware
     * @param MiddlewareRequest $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(MiddlewareRequest $request, Closure $next);
}
