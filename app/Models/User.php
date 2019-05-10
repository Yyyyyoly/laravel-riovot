<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected  $casts = [
        'admin_id' => 'integer',
        'product_id' => 'integer',
        'registered_at' => 'datetime',
    ];
}
