<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone')->comment('手机号');
            $table->string('sms_code')->comment('短信验证码');
            $table->timestamp('expired_at')->comment('验证码过期时间');
            $table->boolean('is_used')->default(0)->comment('是否使用');
            $table->timestamps();

            $table->index(['phone', 'expired_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_codes');
    }
}
