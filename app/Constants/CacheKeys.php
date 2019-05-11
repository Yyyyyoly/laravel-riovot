<?php

namespace App\Constants;


/**
 * 缓存键常量
 *
 * @package App\Constants
 */
class CacheKeys
{

    /**
     * 手机号验证码获取次数
     *
     * @param $phone
     *
     * @return string
     */
    public static function getPhoneSmsCode($phone)
    {
        return "SMS_PHONE_{$phone}_CODE";
    }


}