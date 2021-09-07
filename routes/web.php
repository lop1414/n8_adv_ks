<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// 后台需授权接口
$router->group([
    'prefix' => 'admin',
    'middleware' => ['center_menu_auth', 'admin_request_log', 'access_control_allow_origin']
], function () use ($router) {
    // 任务
    $router->group(['prefix' => 'task'], function () use ($router) {
        $router->post('select', '\\App\Common\Controllers\Admin\TaskController@select');
        $router->post('open', '\\App\Common\Controllers\Admin\TaskController@open');
        $router->post('close', '\\App\Common\Controllers\Admin\TaskController@close');
    });

    // 子任务
    $router->group(['prefix' => 'sub_task'], function () use ($router) {
        // 快手视频上传
        $router->group(['prefix' => 'ks_video_upload'], function () use ($router) {
            $router->post('select', 'Admin\SubTask\TaskKsVideoUploadController@select');
            $router->post('read', 'Admin\SubTask\TaskKsVideoUploadController@read');
        });

        // 快手同步
        $router->group(['prefix' => 'ks_sync'], function () use ($router) {
            $router->post('select', 'Admin\SubTask\TaskKsSyncController@select');
            $router->post('read', 'Admin\SubTask\TaskKsSyncController@read');
        });
    });

    // 快手
    $router->group(['prefix' => 'app'], function () use ($router) {
        $router->post('select', 'Admin\AppController@select');
        $router->post('create', 'Admin\AppController@create');
        $router->post('update', 'Admin\AppController@update');
        $router->post('enable', 'Admin\AppController@enable');
        $router->post('disable', 'Admin\AppController@disable');
    });
    $router->group(['prefix' => 'ks'], function () use ($router) {
        // 账户
        $router->group(['prefix' => 'account'], function () use ($router) {
            $router->post('select', 'Admin\Ks\AccountController@select');
            $router->post('get', 'Admin\Ks\AccountController@get');
            $router->post('read', 'Admin\Ks\AccountController@read');
            $router->post('update', 'Admin\Ks\AccountController@update');
            $router->post('enable', 'Admin\Ks\AccountController@enable');
            $router->post('disable', 'Admin\Ks\AccountController@disable');
            $router->post('delete', 'Admin\Ks\AccountController@delete');
            $router->post('batch_enable', 'Admin\Ks\AccountController@batchEnable');
            $router->post('batch_disable', 'Admin\Ks\AccountController@batchDisable');
        });

        // 视频
        $router->group(['prefix' => 'video'], function () use ($router) {
            $router->post('batch_upload', 'Admin\Ks\VideoController@batchUpload');
        });

        // 广告计划
        $router->group(['prefix' => 'campaign'], function () use ($router) {
            $router->post('select', 'Admin\Ks\KsCampaignController@select');
            $router->post('get', 'Admin\Ks\KsCampaignController@get');
        });

        // 广告组
        $router->group(['prefix' => 'unit'], function () use ($router) {
            $router->post('select', 'Admin\Ks\KsUnitController@select');
            $router->post('get', 'Admin\Ks\KsUnitController@get');
        });


        // 回传策略
        $router->group(['prefix' => 'convert_callback_strategy'], function () use ($router) {
            $router->post('create', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@create');
            $router->post('update', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@update');
            $router->post('select', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@select');
            $router->post('get', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@get');
            $router->post('read', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@read');
        });
    });
    // 点击
    $router->group(['prefix' => 'click'], function () use ($router) {
        $router->post('select', 'Admin\ClickController@select');
        $router->post('callback', 'Admin\ClickController@callback');
    });
});


// 前台接口

$router->group([
    'prefix' => 'front',
    'middleware' => ['api_sign_valid', 'access_control_allow_origin']
], function () use ($router) {
    // 转化
    $router->group(['prefix' => 'convert'], function () use ($router) {
        $router->post('match', '\\App\Common\Controllers\Front\ConvertController@match');
    });

    // 转化回传
    $router->group(['prefix' => 'convert_callback'], function () use ($router) {
        $router->post('get', '\\App\Common\Controllers\Front\ConvertCallbackController@get');
    });

    // 渠道-广告组
    $router->group(['prefix' => 'channel_unit'], function () use ($router) {
        $router->post('select', 'Front\ChannelUnitController@select');
        $router->post('batch_update', 'Front\ChannelUnitController@batchUpdate');
    });
});

$router->group(['middleware' => ['access_control_allow_origin']], function () use ($router) {
    // 点击
    $router->get('front/click', 'Front\AdvClickController@index');
});

// 测试
$router->post('test', 'TestController@test');

