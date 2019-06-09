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
        $updates = UserApplyProduct::groupBy('product_id')
            ->selectRaw('product_id, count(*) as count')
            ->get();

        $product_table_name = Product::getModel()->getTable();

        foreach($updates as $update){
            \DB::update("update {$product_table_name} set `real_download_nums` = {$update->count} 
where id = {$update->product_id}");

        }
    }


}
