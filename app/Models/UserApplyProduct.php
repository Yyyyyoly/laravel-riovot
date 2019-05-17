<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplyProduct extends Model
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
}
