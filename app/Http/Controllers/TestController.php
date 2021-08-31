<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


use App\Services\Ks\KsCampaignService;
use Illuminate\Http\Request;

class TestController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function test(Request $request){
        $key = $request->input('key');
        if($key != 'aut'){
            return $this->forbidden();
        }

        (new KsCampaignService())->sync(['date' => '2021-08-30']);
    }




}
