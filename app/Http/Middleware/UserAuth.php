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
        $admin_has_id = request('admin_hash_id');
        if (empty(session('user_info'))) {
            return redirect(route('loginView', ['admin_hash_id' => $admin_has_id]));
        }

        return $next($request);
    }
}
