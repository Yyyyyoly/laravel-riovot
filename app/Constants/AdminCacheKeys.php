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
     * 获取今日申请排行榜key
     *
     * @param Carbon $now
     *
     * @return string
     */
    public static function getApplyRankKey(Carbon $now)
    {
        return "ADMIN_APPLY_RANK_{$now->toDateString()}";
    }

    /**
     * 获取今日注册排行榜key
     *
     * @param Carbon $now
     *
     * @return string
     */
    public static function getRegisterRankKey(Carbon $now)
    {
        return "ADMIN_REGISTER_RANK_{$now->toDateString()}";
    }

    /**
     * 申请排行榜最后一次更新时间
     *
     * @return string
     */
    public static function getApplyLastUpdateTimestamp()
    {
        return "APPLY_UPDATE_TIMESTAMP";
    }


    /**
     * 注册排行榜最后一次更新时间
     *
     * @return string
     */
    public static function getRegisterLastUpdateTimestamp()
    {
        return "REGISTER_UPDATE_TIMESTAMP";
    }
}