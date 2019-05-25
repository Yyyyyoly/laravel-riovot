<?php

namespace App\Models;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    /**
     * 所属渠道
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class, 'admin_id');
    }

    /**
     * 所属用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
