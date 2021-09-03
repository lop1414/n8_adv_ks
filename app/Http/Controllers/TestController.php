<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


use App\Models\ChannelUnitModel;
use App\Services\AdvClickService;
use App\Services\ChannelUnitService;
use App\Services\Ks\KsUnitService;
use App\Services\Ks\KsCampaignService;
use App\Services\Ks\KsCreativeService;
use App\Services\Ks\KsProgramCreativeService;
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

        $option = [
            'date' => '2021-08-31'
//            'account_ids' => [10157752]
        ];
//        (new KsCampaignService())->sync($option);
//        (new KsAdUnitService())->sync($option);
//        (new KsCreativeService())->sync($option);
//        (new KsProgramCreativeService())->sync($option);
//        (new ChannelUnitService())->sync($option);

        ( new AdvClickService())->pull();
    }







}
