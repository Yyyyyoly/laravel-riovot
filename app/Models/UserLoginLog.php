<?php

namespace App\Models;

use App\User;
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
        $userModel = config('admin.database.users_model');

        return $this->belongsTo($userModel, 'admin_id');
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
