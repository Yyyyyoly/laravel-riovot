<?php

if (!function_exists('success')) {
    /**
     * 返回成功数据
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function success($data = [])
    {
        return response()->json([
            'code'    => 0,
            'success' => true,
            'data'    => empty($data) ? new stdClass : $data,
        ], 200);
    }
}
if (!function_exists('common')) {
    /**
     * Common Helper
     *
     * @return App\Helpers\CommonHelper
     */
    function common()
    {
        return app('common');
    }
}
if (!function_exists('api')) {
    /**
     * API Helper
     *
     * @return App\Helpers\ApiHelper
     */
    function api()
    {
        return app('api');
    }
}
if (!function_exists('oss')) {
    /**
     * OSS Storage Helper
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    function oss()
    {
        return Storage::disk('oss');
    }
}
if (!function_exists('redis')) {
    /**
     * Redis Helper
     *
     * @return \Illuminate\Redis\Connections\Connection
     */
    function redis()
    {
        return \Illuminate\Support\Facades\Redis::connection();
    }
}
if (!function_exists('adminCache')) {
    /**
     * Admin Cache
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    function adminCache()
    {
        return Cache::store('redis_admin');
    }
}