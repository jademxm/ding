<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DingTalkService
{
    protected $webhook;
    protected $secret;

    public function __construct()
    {
        $this->webhook = config('services.dingtalk.webhook');
        $this->secret  = config('services.dingtalk.secret');
    }

    /**
     * 发送文本消息
     */
    public function sendText(string $content, array $atMobiles = [], bool $isAtAll = false): array
    {
        return $this->post([
            'msgtype' => 'text',
            'text'    => ['content' => $content],
            'at'      => ['atMobiles' => $atMobiles, 'isAtAll' => $isAtAll],
        ]);
    }

    /**
     * 发送 Markdown 消息
     */
    public function sendMarkdown(string $title, string $text, array $atMobiles = [],bool $isAtAll = false): array
    {
        $atText = '';
        foreach ($atMobiles as $mobile) {
            $atText .= " @{$mobile}";
        }
        $body = [
            'msgtype'  => 'markdown',
            'markdown' => [
                'title' => $title,
                'text'  => $text . $atText,
            ],
            'at' => [
                'atMobiles' => $atMobiles,
                'isAtAll'   => $isAtAll,
            ],
        ];

        return $this->post($body);
    }

    /**
     * 加签 + POST
     */
    protected function post(array $body): array
    {
        $timestamp = now()->getTimestampMs();          // 毫秒
        $sign      = $this->generateSign($timestamp);
        $url       = $this->webhook
            . '&timestamp=' . $timestamp
            . '&sign=' . urlencode($sign);

        try {
            $resp = Http::asJson()->post($url, $body)->json();
        } catch (\Throwable $e) {
            Log::error('DingTalk send failed', ['msg' => $e->getMessage()]);
            $resp = ['errcode' => -1, 'errmsg' => $e->getMessage()];
        }

        return $resp;
    }

    /**
     * HmacSHA256 加签
     */
    protected function generateSign(int $timestamp): string
    {
        $stringToSign = $timestamp . "\n" . $this->secret;
        return base64_encode(
            hash_hmac('sha256', $stringToSign, $this->secret, true)
        );
    }
}