<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'admin_id'      => 'integer',
        'product_id'    => 'integer',
        'registered_at' => 'datetime',
    ];


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
     * 解析加密后的渠道id
     *
     * @param $id
     *
     * @return string
     */
    public static function decodeAdminId($id)
    {
        $hash = new Hashids(config('app.name'), 6);

        return intval($hash->decode($id)[0]) ?? 0;
    }


    /**
     * 加密渠道id
     *
     * @param $id
     *
     * @return string
     */
    public static function encodeAdminId($id)
    {
        $hash = new Hashids(config('app.name'), 6);

        return $hash->encode($id);
    }

}
