<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * 产品类别
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'type_id');
    }


    /**
     * 获取logo真实路径
     *
     * @param $icon_url
     *
     * @return string
     */
    public static function transferIconUrl($icon_url)
    {
        $disk = config('admin.upload.disk');

        return config("filesystems.disks.{$disk}.url") . '/' . $icon_url;
    }
}
