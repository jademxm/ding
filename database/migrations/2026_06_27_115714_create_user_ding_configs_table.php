<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_ding_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name')->comment('配置名称');
            $table->string('webhook', 500)->comment('Webhook地址');
            $table->string('secret', 255)->comment('加签密钥');
            $table->tinyInteger('status')->default(1)->comment('状态:1启用,2禁用');
            $table->timestamps();
            $table->comment('用户钉钉机器人配置');
            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ding_configs');
    }
};
