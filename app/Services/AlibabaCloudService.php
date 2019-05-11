<?php

namespace App\Services;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AlibabaCloudService
{

    /**
     * 发送注册短信
     *
     * @param $phone
     * @param $code
     *
     * @return bool
     */
    public static function sendRegisterSmsCode($phone, $code)
    {
        $template_code = config('ali.aliyun_register_template');

        return static::sendSmsCode($phone, $code, $template_code);
    }

    /**
     * 发送密码重置短信
     *
     * @param $phone
     * @param $code
     *
     * @return bool
     */
    public static function sendResetPasswordSmsCode($phone, $code)
    {
        $template_code = config('ali.aliyun_reset_template');

        return static::sendSmsCode($phone, $code, $template_code);
    }


    /**
     * 发送类型短信
     *
     * @param $phone
     * @param $code
     * @param $template_code
     *
     * @return bool
     */
    public static function sendSmsCode($phone, $code, $template_code)
    {
        $access_key_id = config('ali.aliyun_access_key_id');
        $access_key_secret = config('ali.aliyun_access_key_secret');
        $sig_name = config('ali.aliyun_sig_name');

        try {
            AlibabaCloud::accessKeyClient($access_key_id, $access_key_secret)
                ->regionId('cn - hangzhou')// replace regionId as you need
                ->asGlobalClient();

            $result = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'PhoneNumbers'  => $phone,
                        'SignName'      => $sig_name,
                        'TemplateCode'  => $template_code,
                        'TemplateParam' => json_encode(['code' => $code]),
                    ],
                ])
                ->request()
                ->toArray();

            if ($result['Message'] != "OK") {
                \Log::error("{$phone}阿里云短信发送失败，错误信息——{$result['Message']}" . PHP_EOL);

                return false;
            }

        } catch (ClientException $e) {
            \Log::error($e->getErrorMessage() . PHP_EOL);

            return false;
        } catch (ServerException $e) {
            \Log::error($e->getErrorMessage() . PHP_EOL);

            return false;
        }

        return true;
    }
}