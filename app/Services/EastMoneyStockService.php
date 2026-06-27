<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EastMoneyStockService
{
    protected const BASE = 'https://push2.eastmoney.com/api/qt/stock/get';

    public function getQuote(string $code): ?array
    {
        $secid = $this->toSecId($code);

        $resp = Http::withHeaders([
            'Referer'    => 'https://quote.eastmoney.com/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ])
            // ★ 关键：强制 IPv4 + 超时
            ->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
                'timeout'         => 10,
                'connect_timeout' => 5,
            ])
            ->get(self::BASE, [
                'secid'  => $secid,
                'fields' => 'f43,f44,f45,f46,f47,f48,f57,f58,f60,f169,f170,f171',
                'ut'     => 'fa5fd1943a7b68a57ecm80eafb28b673',
            ]);

        $data = $resp->json('data');
        if (!$data) {
            return null;
        }

        return [
            'code'       => $code,
            'name'       => $data['f58'] ?? '',
            'price'      => $data['f43'] ?? null,
            'open'       => $data['f44'] ?? null,
            'prev_close' => $data['f60'] ?? null,
            'change_pct'=> $data['f45'] ?? null,
            'volume'     => $data['f46'] ?? null,
            'amount'     => $data['f47'] ?? null,
            'limit_up'   => $data['f169'] ?? null,
            'limit_down'=> $data['f170'] ?? null,
        ];
    }

    protected function toSecId(string $code): string
    {
        if (str_starts_with($code, 'sh')) {
            return '1.' . substr($code, 2);
        }
        if (str_starts_with($code, 'sz')) {
            return '0.' . substr($code, 2);
        }
        return $code;
    }
}