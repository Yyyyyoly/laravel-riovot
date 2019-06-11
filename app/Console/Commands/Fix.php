<?php

namespace App\Console\Commands;

use App\Constants\AdminCacheKeys;
use App\Models\Product;
use App\Models\UserApplyProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Fix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'bug修復';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $products = Product::get();
        $user_apply_table = UserApplyProduct::getModel()->getTable();
        foreach ($products as $product) {
            \DB::statement("update {$user_apply_table} set product_name = '{$product->name}' where product_id  = {$product->id}");
        }
    }


}
