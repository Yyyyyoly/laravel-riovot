<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// WEB API
Route::prefix('web')
    ->middleware('cors')
    ->group(function () {
        // 授权
        Route::prefix('user')
            ->namespace('User')
            ->group(function () {
                // 用户详情页
                Route::get('info/{admin_hash_id}', 'UserController@infoView')->name('infoView');

                // 获取短信验证码
                Route::post('sms', 'UserController@sms')->name('sms');
                // 登录 or 注册 or  忘记密码页面
                Route::get('login/{admin_hash_id}', 'UserController@loginView')->name('loginView');
                // 登录
                Route::post('login', 'UserController@login')->name('login');
                // 注册
                Route::post('register', 'UserController@register')->name('register');
                // 忘记密码
                Route::post('forget', 'UserController@forget')->name('forget');
                // 退出登录
                Route::post('logout', 'UserController@logout')->name('logout');
            });

        // 第三方产品申请
        Route::prefix('product')
            ->namespace('Product')
            ->group(function () {
                // 产品列表页
                Route::get('info/{admin_hash_id}', 'ProductController@productView')->name('productView');

                // 产品申请
                Route::get('apply/{admin_hash_id}', 'ProductController@applyView')
                    ->middleware('user.auth')->name('product');
            });

    });


Route::get('/', function () {
    return view('welcome');
});
