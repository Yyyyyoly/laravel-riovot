<?php

namespace App\Http\Controllers\User;

use App\Constants\AdminCacheKeys;
use App\Models\AdminUser;
use App\Constants\CacheKeys;
use App\Models\SmsCode;
use App\Models\UserLoginLog;
use App\Services\AlibabaCloudService;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 获取短信验证码
     */
    public function sms()
    {
        $now = Carbon::now();
        $phone = request('phone');
        $type = request('type', 'register');
        if (empty($phone)) {
            return response()->json(['success' => false, 'reason' => '手机号不能为空'], 400);
        }

        // 检查最近5分钟内获取频次
        $cache_key = CacheKeys::getPhoneSmsCode($phone);
        $num = (int)cache($cache_key);
        if ($num >= 3) {
            return response()->json(['success' => false, 'reason' => '获取短信验证码次数频繁，请稍后再试'], 500);
        }

        // 生成有效验证码
        $sms = SmsCode::wherePhone($phone)->where('expired_at', '>=', $now)->where('is_used', 0)->first();
        if (empty($sms) || empty($sms->id)) {
            $rand = mt_rand(0, 999999);
            $sms_code = str_pad($rand, 6, "0", STR_PAD_LEFT);
            $sms = new SmsCode();
            $sms->phone = $phone;
            $sms->sms_code = $sms_code;
        } else {
            $sms_code = $sms->sms_code;
        }
        $sms->expired_at = $now->addMinute(5);
        $sms->save();

        // 发送验证码
        if ($type == 'register') {
            $rtn = AlibabaCloudService::sendRegisterSmsCode($phone, $sms_code);
        } else {
            $rtn = AlibabaCloudService::sendResetPasswordSmsCode($phone, $sms_code);
        }
        if (!$rtn) {
            return response()->json(['success' => false, 'reason' => '验证码发送失败，请重试'], 500);
        }

        if ($num == 0) {
            cache()->set($cache_key, 1, 300);
        } else {
            cache()->increment($cache_key, 1);
        }

        return response()->json(['success' => true], 200);
    }


    /**
     * 用户信息页
     *
     * @param $admin_hash_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function infoView($admin_hash_id)
    {
        $user_info = session('user_info', []);
        $user_id = $user_info['user_id'] ?? 0;
        $user_name = $user_info['name'] ?? '';
        $phone = $user_info['phone'] ?? '';

        return view('info', [
            'admin_hash_id' => $admin_hash_id,
            'user_name'     => $user_name,
            'phone'         => $phone,
            'is_login'      => $user_id == 0 ? 0 : 1,
        ]);
    }


    /**
     * 登录页面
     *
     * @param $admin_hash_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loginView($admin_hash_id)
    {
        $product_id = request('product_id', 0);

        return view('login', [
            'admin_hash_id' => $admin_hash_id,
            'product_id'    => $product_id,
        ]);
    }


    /**
     * 登录
     */
    public function login()
    {
        $phone = request('phone');
        $password = request('password');
        $admin_hash_id = request('admin_hash_id', 0);

        if (empty($phone) || empty($password)) {
            return response()->json(['success' => false, 'reason' => '请输入手机号和密码'], 400);
        }
        if (empty($admin_hash_id)) {
            return response()->json(['success' => false, 'reason' => '请从您的专属渠道链接进入后登录！'], 500);
        }

        // 验证专属链接有效性
        $admin_id = User::decodeAdminId($admin_hash_id);
        $admin_info = AdminUser::whereId($admin_id)->first();
        if (empty($admin_info) || empty($admin_info->id)) {
            return response()->json(['success' => false, 'reason' => '请从您的专属渠道链接进入后登录！'], 500);
        }

        // 检查用户及对应密码
        $user = User::wherePhone($phone)->first();
        if (empty($user) || empty($user->id)) {
            return response()->json(['success' => false, 'reason' => '请先去注册！', 500]);
        }
        if (password_verify($password, $user->password)) {
            return response()->json(['success' => false, 'reason' => '账号名或者密码错误！', 500]);
        }

        // 登录成功,写入session和客户登录日志
        $this->addLoginLog($user, $admin_id);

        return response()->json(['success' => true], 200);
    }


    /**
     * 记录登录日志并更新session
     *
     * @param User $user
     * @param      $admin_id
     *
     * @return bool
     */
    private function addLoginLog(User $user, $admin_id)
    {
        session([
            'user_info' => [
                'user_id' => $user->id,
                'name'    => $user->name,
                'phone'   => $user->phone,
            ],
        ]);

        $login_log = new UserLoginLog();
        $login_log->admin_id = $admin_id;
        $login_log->user_id = $user->id;

        return $login_log->save();
    }


    /**
     * 注册
     */
    public function register()
    {
        $now = Carbon::now();
        $phone = request('phone');
        $sms_code = request('sms_code');
        $admin_hash_id = request('admin_hash_id');
        $product_id = request('product_id', 0);
        $name = request('name');
        $password = request('password');
        $age = request('age');
        $ant_scores = request('ant_scores');

        if (empty($phone) || empty($sms_code) || empty($admin_hash_id) ||
            empty($name) || empty($password) || empty($age) || empty($ant_scores)) {
            return response()->json(['success' => false, 'reason' => '参数不能为空！'], 400);
        }

        // 验证渠道id的有效性
        $admin_id = User::decodeAdminId($admin_hash_id);
        $admin_info = AdminUser::whereId($admin_id)->first();
        if (empty($admin_info) || empty($admin_info->id)) {
            return response()->json(['success' => false, 'reason' => '请从您的专属渠道链接进入后注册！'], 500);
        }

        // 查询有效验证码
        $sms = SmsCode::wherePhone($phone)->where('expired_at', '>=', $now)->where('is_used', 0)->first();
        if (empty($sms) || empty($sms->id)) {
            return response()->json(['success' => false, 'reason' => '验证码已经失效！'], 400);
        }

        if ($sms_code != $sms->sms_code) {
            return response()->json(['success' => false, 'reason' => '验证码错误！'], 400);
        }

        // 保存验证码为已经使用
        $sms->is_used = 1;
        $sms->save();

        // 注册用户
        $user = User::wherePhone($phone)->first();
        if ($user && $user->id) {
            return response()->json(['success' => false, 'reason' => '您已经注册，请直接登录！'], 500);
        }
        $user = new User();
        $user->phone = $phone;
        $user->admin_id = $admin_id;
        $user->product_id = $product_id;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->name = $name;
        $user->ant_scores = $ant_scores > 0 ? $ant_scores : 0;
        $user->age = $age > 0 ? $age : 0;
        $user->registered_at = Carbon::now();

        if ($user->save()) {
            // 登录成功,写入session和客户登录日志
            $this->addLoginLog($user, $admin_id);

            // 实时更新注册排行榜数据
            $redis_key = AdminCacheKeys::getRegisterRankKey($now);
            redis()->zincrby($redis_key, 1, $admin_id);
            redis()->expireat($redis_key, $now->copy()->addDays(2)->startOfDay());

            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false, 'reason' => '服务器开小差，请稍后再试'], 500);
        }
    }


    /**
     * 忘记密码
     */
    public function forget()
    {
        $now = Carbon::now();
        $phone = request('phone');
        $sms_code = request('sms_code');
        $password = request('password');

        if (empty($phone) || empty($sms_code)) {
            return response()->json(['success' => false, 'reason' => '参数不能为空！'], 400);
        }

        // 查询有效验证码
        $sms = SmsCode::wherePhone($phone)->where('expired_at', '>=', $now)->where('is_used', 0)->first();
        if (empty($sms) || empty($sms->id)) {
            return response()->json(['success' => false, 'reason' => '验证码已经失效！'], 400);
        }

        if ($sms_code != $sms->sms_code) {
            return response()->json(['success' => false, 'reason' => '验证码错误！'], 400);
        }

        // 保存验证码为已经使用
        $sms->is_used = 1;
        $sms->save();

        // 注册用户
        $user = User::wherePhone($phone)->first();
        if (empty($user) || empty($user->id)) {
            return response()->json(['success' => false, 'reason' => '请先去注册！'], 500);
        }
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->save();

        // 强制退出
        session()->flush();

        return response()->json(['success' => true], 200);
    }

    /**
     * 登出
     */
    public function logout()
    {
        $user_info = session('user_info', []);

        if ($user_info) {
            session()->flush();
        }

        return response()->json(['success' => true], 200);
    }
}
