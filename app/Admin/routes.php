<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    // 产品类型管理
    $router->resource('product-type', ProductTypeController::class);

    // 产品管理
    $router->resource('product', ProductController::class);

    // 渠道管理
    $router->resource('user', AdminController::class);

    // 渠道统计报表
    $router->get('user-statistics', 'UserStatisticsController@index');

    // 客户列表
    $router->resource('customer', UserController::class);

    // 客户登录日志
    $router->resource('customer-login', UserLoginController::class);

    // 客户申请日志
    $router->resource('customer-apply', UserApplyProductController::class);
});
