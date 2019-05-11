<?php

namespace App;

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
     * 解析加密后的渠道id
     *
     * @param $id
     *
     * @return array
     */
    public static function decodeAdminId($id)
    {
        $hash = new Hashids();

        return $hash->decode($id);
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
        $hash = new Hashids();

        return $hash->encode($id);
    }
}
