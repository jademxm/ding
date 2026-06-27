<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserDingConfig;
use App\Models\UserStock;
use App\Services\DingTalkService;
use App\Services\SinaStockService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckStockAlert extends Command
{
    protected $signature = 'stock:check';
    protected $description = '检查用户股票池价格并推送钉钉预警';

    public function handle(): void
    {
        $now = Carbon::now('Asia/Shanghai');
        $isTradeTime = $now->isWeekday()
            && (
                ($now->between(Carbon::parse('09:30'), Carbon::parse('11:30')))
                || ($now->between(Carbon::parse('13:00'), Carbon::parse('15:00')))
            );

        if (!$isTradeTime) {
            Log::info('非交易时段，跳过');
            return;
        }

        // 1. 获取所有启用用户
        $users = User::where('status', User::STATUS_ENABLE)->get();
        if ($users->isEmpty()) {
            Log::info('没有启用的用户');
            return;
        }

        $ding = new DingTalkService();
        $stockSvc = new SinaStockService();

        foreach ($users as $user) {

            // 2. 获取用户股票池（启用状态）
            $stocks = UserStock::where('user_id', $user->id)
                ->where('status', UserStock::STATUS_ENABLE)
                ->get();

            if ($stocks->isEmpty()) {
                continue;
            }

            // 3. 批量查行情（新浪支持逗号拼接）
            $codes = $stocks->pluck('code')->toArray();
            $quotes = $stockSvc->getQuotes($codes);
            if (empty($quotes)) {
                Log::info("股票行情获取失败", ['codes' => $codes, 'user_name' => $user->name]);
                continue;
            }
            // 4. 获取用户钉钉配置（启用）
            $dingConfig = UserDingConfig::where('user_id', $user->id)
                ->where('status', UserDingConfig::STATUS_ENABLE)
                ->first();

            foreach ($stocks as $stock) {
                $code = $stock->code;
                if (!isset($quotes[$code])) {
                    continue;
                }

                $q = $quotes[$code];
                $price = $q['price'];
                $name = $q['name'] ?? $stock->name;
                $change_pct = $q['change_pct'] ?? null;

                // 5. 判断是否超出 [min, max] 区间
                $min = $stock->min; // 如果没有 min/max 字段，用 code 范围判断
                $max = $stock->max;
                $alert = false;
                $msg = '';

                // 同一价位且距上次预警不足 30 分钟 → 跳过
                if (
                    $stock->last_alert_price !== null
                    && abs($stock->last_alert_price - $price) < 0.001
                    && $stock->last_alert_at
                    && $stock->last_alert_at->diffInMinutes(now()) < 30
                ) {
                    Log::info("{$user->name},{$stock->name},{$price},同一价位且距上次预警不足 30 分钟 → 跳过");
                    continue;
                }

                $date = Carbon::now()->format('m-d H:i');
                if ($price <= $min || $price >= $max) {
                    $alert = true;
                    $msg = <<<TEXT
{$name}
💰 现价：{$price}
🌐 幅度：{$change_pct}
🕐 时间： {$date}
TEXT;
                }

                if (!$alert) {
                    continue;
                }
                // 6. 推钉钉
                if ($dingConfig) {
                    try {
                        $ding->setConfig($dingConfig->webhook, $dingConfig->secret);
                        $resp = $ding->sendText($msg, [$user->phone]);
                        if($resp["errcode"] == 0){
                            // 推送成功后
                            $stock->update([
                                'last_alert_at'   => now(),
                                'last_alert_price'=> $price,
                            ]);
                        }
                        Log::info("钉钉预警推送成功", [
                            'user' => $user->id,
                            'stock' => $code,
                            'price' => $price,
                            'resp' => $resp,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error("钉钉预警推送失败", [
                            'user' => $user->id,
                            'stock' => $code,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } else {
                    // 无钉钉配置，推送到默认群
                    try {
                        $resp = $ding->sendText($msg, [$user->phone]);
                        if($resp["errcode"] == 0){
                            // 推送成功后
                            $stock->update([
                                'last_alert_at'   => now(),
                                'last_alert_price'=> $price,
                            ]);
                        }
                        Log::info("钉钉预警推送成功,无配置", [
                            'user' => $user->id,
                            'stock' => $code,
                            'price' => $price,
                            'resp' => $resp,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error("默认钉钉推送失败", ['error' => $e->getMessage()]);
                    }
                }

                $this->info("已推送预警: {$name}({$code}) 价格 {$price}");
            }
        }

        $this->info('股票预警检查完成');
    }
}