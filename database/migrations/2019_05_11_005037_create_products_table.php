<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('type_id')->comment('产品所属类型');
            $table->string('name')->comment('产品名称');
            $table->text('url')->comment('产品链接');
            $table->string('desc')->comment('产品简介');
            $table->integer('icon_id')->comment('产品icon');
            $table->string('icon_url')->comment('产品url快照');
            $table->integer('fake_download_nums')->default(0)->comment('虚假下载量');
            $table->integer('order')->default(1)->comment('产品排序');
            $table->boolean('is_show')->default(1)->comment('是否展示');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
