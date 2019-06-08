<?php

namespace App\Http\Controllers\Product;

use App\Constants\AdminCacheKeys;
use App\FakeDatas\UserPhone;
use App\Models\AdminUser;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\UserApplyProduct;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ProductController extends Controller
{

    /**
     * 产品列表
     *
     * @param $admin_hash_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productView($admin_hash_id)
    {

        return view('product', [
            'admin_hash_id' => $admin_hash_id,
            'product_list'  => Product::getProductList(),
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
        $results = [];

        $time_start = now()->addDays(-7)->timestamp;
        $time_end = now()->timestamp;

        $fake_phone = new UserPhone();
        $phone_list_1 = $fake_phone->random(50);
        $money_list = [20000, 25000, 30000, 35000, 40000, 45000];
        foreach ($phone_list_1 as $phone) {
            $money_key = array_rand($money_list, 1);
            $time = mt_rand($time_start, $time_end);
            $results[] = [
                'time'  => timeDisplayTrans(Carbon::createFromTimestamp($time)),
                'title' => "{$phone}申请的{$money_list[$money_key]}元借款成功到",
            ];
        }


        $phone_list_2 = $fake_phone->random(20);
        $money_list_2 = [50000, 60000, 70000, 80000, 90000, 100000];
        foreach ($phone_list_2 as $phone) {
            $money_key = array_rand($money_list_2, 1);
            $time = mt_rand($time_start, $time_end);
            $results[] = [
                'time'  => timeDisplayTrans(Carbon::createFromTimestamp($time)),
                'title' => "{$phone}申请的{$money_list_2[$money_key]}元借款成功到",
            ];
        }

        shuffle($results);

        return $results;
    }


    /**
     * 跳转到第三方申请页并记录申请uv
     *
     * @param $admin_hash_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyView($admin_hash_id)
    {
        $user = session('user_info');
        $user_id = $user['user_id'] ?? 0;
        $product_id = request('product_id');
        $now = Carbon::now();

        // 查询产品是否有效
        $product_table_name = Product::getModel()->getTable();
        $type_table_name = ProductType::getModel()->getTable();
        $products = Product::from("{$product_table_name} as a")
            ->join("{$type_table_name} as b", 'a.type_id', '=', 'b.id')
            ->where('a.is_show', 1)
            ->where('b.is_show', 1)
            ->where('a.id', $product_id)
            ->first();
        if (empty($product_id) || empty($products->id)) {
            return view('error', ['error_msg' => '产品已经下架，无法申请！']);
        }

        // 验证渠道id的有效性
        $admin_id = User::decodeAdminId($admin_hash_id);
        $admin_info = AdminUser::whereId($admin_id)->first();
        if (empty($admin_info) || empty($admin_info->id)) {
            return view('error', ['error_msg' => '请从您的专属渠道链接进入后申请！']);
        }

        // 记录申请uv
        $apply_log = UserApplyProduct::whereUserId($user_id)
            ->whereProductId($product_id)
            ->first();
        if (empty($apply_log) || empty($apply_log->id)) {
            $apply_log = new UserApplyProduct();
            $apply_log->product_id = $product_id;
            $apply_log->user_id = $user_id;
            $apply_log->admin_id = $admin_id;
            $apply_log->save();

            // 真实下载量+1
            $products->increment('real_download_nums');

            try {
                // 实时更新申请排行榜数据
                $redis_key = AdminCacheKeys::getApplyRankKey($now);
                redis()->zincrby($redis_key, 1, $admin_id);
                redis()->expireat($redis_key, $now->copy()->addDays(2)->startOfDay()->getTimestamp());
            } catch (\Exception $ex) {
            }

        }

        // 跳第三方
        return redirect($products->url);
    }
}
