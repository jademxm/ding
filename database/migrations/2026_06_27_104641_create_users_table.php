<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('用户名');
            $table->string('phone', 20)->comment('手机号');
            $table->tinyInteger('status')->default(User::STATUS_ENABLE)->comment('状态');
            $table->decimal('balance', 10, 2)->default(0.00)->comment('余额');
            $table->timestamps();
            $table->comment('用户表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
