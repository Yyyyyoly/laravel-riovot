<?php

namespace App\Http\Middleware;

use Closure;

class UserAuth
{

    /**
     * 验证用户登录
     *
     * @param         $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, Closure $next)
    {
        $admin_hash_id = request('admin_hash_id');
        if (empty(session('user_info'))) {
            $product_id = request('product_id');
            $route = route('loginView', ['admin_hash_id' => $admin_hash_id]);

            // 如果带了产品id
            if (!empty($product_id)) {
                $route .= '?product_id=' . $product_id;
            }

            return redirect($route);
        }

        return $next($request);
    }
}
