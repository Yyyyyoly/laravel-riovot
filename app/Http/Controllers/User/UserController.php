<?php

namespace App\Http\Controllers\User;

use App\Models\AdminUser;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\UserLoginLog;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 获取短信验证码
     */
    public function sms()
    {

    }


    /**
     * 首页
     *
     * @param $admin_hash_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function homeView($admin_hash_id)
    {
        // 查询产品列表
        $product_table_name = Product::getModel()->getTable();
        $type_table_name = ProductType::getModel()->getTable();
        $products = Product::from("{$product_table_name} as a")
            ->join("{$type_table_name} as b", 'a.type_id', '=', 'b.id')
            ->where('a.is_show', 1)
            ->where('b.is_show', 1)
            ->orderByDesc('b.order')
            ->orderByDesc('a.order')
            ->selectRaw('b.id as type_id, b.name as type_name, a.name as product_name, a.id as product_id, url, `desc`, icon_url, fake_download_nums')
            ->get();
        $product_list = [];
        foreach ($products as $product) {
            if (empty($product[$product->type_id])) {
                $product[$product->type_id] = [
                    'type_name' => $product->type_name,
                    'products'  => [],
                ];
            }

            $product[$product->type_id]['products'][] = [
                'id'            => $product->product_id,
                'name'          => $product->product_name,
                'desc'          => $product->desc,
                'icon_url'      => $product->icon_url,
                'download_nums' => $product->fake_download_nums,
            ];
        }


        return view('home', [
            'admin_hash_id' => $admin_hash_id,
            'product_list'  => $product_list,
            'fake_list'     => $this->getFakeList(),
        ]);
    }


    /**
     * 滚动假数据
     *
     * @return array
     */
    private function getFakeList()
    {
        return [
            ['time' => '刚刚', 'title' => '133****3562申请的25000元借款成功到账'],
            ['time' => '2分钟前', 'title' => '138****9388申请的10000元借款成功到账'],
            ['time' => '5分钟前', 'title' => '187****6265申请的40000元借款成功到账'],
            ['time' => '1小时前', 'title' => '187****1063申请的10000元借款成功到账'],
            ['time' => '30分钟前', 'title' => '130****7019申请的50000元借款成功到账'],
        ];
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
        $user_id = $user_info['id'] ?? 0;
        $user_name = $user_info['user_name'] ?? '';
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
        return view('auth.login', [
            'admin_hash_id' => $admin_hash_id,
        ]);
    }


    /**
     * 登录
     */
    public function login()
    {
        $phone = request('phone');
        $password = request('password');
        if (empty($phone) || empty($password)) {
            return request()->json(400, ['success' => false, 'reason' => '请输入手机号和密码']);
        }

        // 检查渠道admin_id是否真实有效
        $admin_hash_id = request('admin_hash_id', 0);
        if (empty($admin_hash_id)) {
            return request()->json(500, ['success' => false, 'reason' => '请从您的专属渠道链接进入后登录！']);
        }

        $admin_id = User::decodeAdminId($admin_hash_id);
        $admin_info = AdminUser::whereId($admin_id)->first();
        if (empty($admin_info) || empty($admin_info->id)) {
            return request()->json(500, ['success' => false, 'reason' => '请从您的专属渠道链接进入后登录！']);
        }

        // 检查用户及对应密码
        $user = User::wherePhone($phone)->first();
        if (empty($user) || empty($user->id)) {
            return request()->json(500, ['success' => false, 'reason' => '请先去注册！']);
        }
        if (password_verify($password, $user->password)) {
            return request()->json(500, ['success' => false, 'reason' => '账号名或者密码错误！']);
        }

        // 登录成功,写入session和客户登录日志
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
        $login_log->save();

        return request()->json(200, ['success' => true]);
    }


    /**
     * 注册
     */
    public function register()
    {

    }


    /**
     * 忘记密码
     */
    public function forget()
    {

    }

    /**
     * 登出
     */
    public function logout()
    {
        $user_info = session('user_info', []);

        if ($user_info) {
            session()->forget('user_info');
        }

        return request()->json(200, ['success' => true]);
    }
}
