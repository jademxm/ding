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
            $table->integer('code')->comment('股票代号');
            $table->tinyInteger('status')->default(\App\Models\UserStock::STATUS_ENABLE)->comment('状态');
            $table->integer('order')->default(0)->comment('排序');
            $table->timestamps();
            $table->comment('用户股票池');
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
