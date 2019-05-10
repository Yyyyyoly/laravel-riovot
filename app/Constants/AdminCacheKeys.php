<?php

namespace App\Constants;

use Carbon\Carbon;

/**
 * 缓存键常量
 *
 * @package App\Constants
 */
class AdminCacheKeys
{

    /**
     * 获取资讯源发布 今日跳过key
     *
     * @return string
     */
    public static function getNewsReleaseNextButtonSkipKey(Carbon $now)
    {
        return "ADMIN_NEWS_RELEASE_{$now->toDateString()}_NEXT_BUTTON_SKIP";
    }

    /**
     * 获取视频源发布 今日跳过key
     *
     * @return string
     */
    public static function getVideoReleaseNextButtonSkipKey(Carbon $now)
    {
        return "ADMIN_VIDEO_RELEASE_{$now->toDateString()}_NEXT_BUTTON_SKIP";
    }
}