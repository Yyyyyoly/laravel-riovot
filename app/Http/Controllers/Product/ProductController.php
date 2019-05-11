<?php

namespace App\Http\Controllers\Product;

use App\Models\AdminUser;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\UserApplyProduct;
use App\User;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * 跳转到第三方申请页并记录申请uv
     *
     * @param $admin_hash_id
     * @param $product_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyView($admin_hash_id, $product_id)
    {
        $user = session('user_info');
        $user_id = $user['id'] ?? 0;

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
        }

        // 跳第三方
        return redirect($products->url);
    }
}
