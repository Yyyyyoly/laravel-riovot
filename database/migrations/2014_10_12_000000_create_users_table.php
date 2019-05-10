<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('admin_id')->comment('来源渠道人员id');
            $table->integer('product_id')->comment('来源产品id');
            $table->string('phone')->unique()->comment('手机号');
            $table->string('name')->comment('姓名');
            $table->string('password');
            $table->integer('age')->comment('年龄');
            $table->integer('ant_scores')->comment('芝麻分');
            $table->timestamp('registered_at')->comment('注册时间');

            $table->timestamps();

            $table->index(['registered_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
