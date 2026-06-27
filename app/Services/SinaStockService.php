<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SinaStockService
{
    /**
     * @param string $code 6位A股代码 如 600519 / 000630 / 002132
     */
    public function getQuote(string $code): ?array
    {
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return null;
        }

        $prefix = str_starts_with($code, '6') ? 'sh' : 'sz';
        $symbol = $prefix . $code;

        $resp = Http::withHeaders([
            'Referer'    => 'https://finance.sina.com.cn/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->withOptions([
            'curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4],
        ])->get("https://hq.sinajs.cn/list={$symbol}");

        $body = $resp->body();

        if (!preg_match('/"(.+?)"/', $body, $m)) {
            return null;
        }

        // Laravel Http 默认不转编码，手动处理
        $raw = mb_convert_encoding($m[1], 'UTF-8', 'GBK');
        $f = explode(',', $raw);

        if (!isset($f[3]) || $f[3] === '') {
            return null;
        }

        $prev = (float)$f[2];
        $now  = (float)$f[3];

        return [
            'code'       => $code,
            'name'       => $f[0],
            'price'      => $now,
            'open'       => (float)$f[1],
            'prev_close' => $prev,
            'high'       => (float)$f[4],
            'low'        => (float)$f[5],
            'volume'     => (int)($f[8] ?? 0),
            'change'     => round($now - $prev, 4),
            'change_pct'=> $prev != 0 ? round(($now - $prev) / $prev * 100, 2) : 0,
            'time'       => ($f[30] ?? '') . ' ' . ($f[31] ?? ''),
        ];
    }

    /**
     * 批量查询
     */
    public function getQuotes(array $codes): array
    {
        $result = [];
        foreach ($codes as $code) {
            $q = $this->getQuote($code);
            if ($q) $result[$code] = $q;
        }
        return $result;
    }
}