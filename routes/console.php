<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 股票预警检查 —— 每分钟一次，防重叠
Schedule::command('stock:check')
    ->everyMinute()
    ->withoutOverlapping(10)   // 锁 10 分钟
    ->timezone('Asia/Shanghai')
    ->runInBackground();
