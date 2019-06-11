<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserApplyProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_apply_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->comment('产品id');
            $table->string('product_name')->comment('产品名称');
            $table->bigInteger('user_id')->comment('客户id');
            $table->integer('admin_id')->comment('渠道人员id');
            $table->timestamps();

            $table->unique(['user_id', 'product_name']);
            $table->index(['admin_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_apply_products');
    }
}
