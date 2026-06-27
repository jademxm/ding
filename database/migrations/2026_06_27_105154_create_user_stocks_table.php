<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name')->comment('股票名称');
            $table->string('code')->comment('股票代号');
            $table->decimal('min',10,2)->comment('监控最小值');
            $table->decimal('max',10,2)->comment('监控最大值');
            $table->tinyInteger('status')->default(\App\Models\UserStock::STATUS_ENABLE)->comment('状态');
            $table->integer('order')->default(0)->comment('排序');
            $table->dateTime('last_alert_at')->nullable()->comment('上次预警时间');
            $table->decimal('last_alert_price', 10, 2)->nullable()->comment('上次预警价');
            $table->timestamps();
            $table->comment('用户股票池');
            $table->unique(['user_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stocks');
    }
};
