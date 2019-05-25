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
        return self::getImageHostPrefix() . '/' . $icon_url;
    }


    /**
     * 获取图片域名服务器前缀地址
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getImageHostPrefix()
    {
        $disk = config('admin.upload.disk');

        return config("filesystems.disks.{$disk}.url");
    }


    /**
     * 查询开启的产品列表
     *
     * @return array
     */
    public static function getProductList()
    {
        // 查询产品列表
        $product_table_name = Product::getModel()->getTable();
        $type_table_name = ProductType::getModel()->getTable();
        $products = \DB::table("{$product_table_name} as a")
            ->join("{$type_table_name} as b", 'a.type_id', '=', 'b.id')
            ->where('a.is_show', 1)
            ->where('b.is_show', 1)
            ->orderByDesc('b.order')
            ->orderByDesc('a.top')
            ->orderByDesc('a.order')
            ->selectRaw('b.id as type_id, b.name as type_name, a.name as product_name, a.id as product_id, url, `desc`, icon_url, fake_download_nums, real_download_nums')
            ->get();
        $product_list = [];
        foreach ($products as $product) {
            if (empty($product_list[$product->type_id])) {
                $product_list[$product->type_id] = [
                    'type_name' => $product->type_name,
                    'products'  => [],
                ];
            }

            $product_list[$product->type_id]['products'][] = [
                'id'            => $product->product_id,
                'name'          => $product->product_name,
                'desc'          => $product->desc,
                'icon_url'      => static::transferIconUrl($product->icon_url),
                'download_nums' => $product->real_download_nums + $product->fake_download_nums,
            ];
        }

        return $product_list;
    }
}
