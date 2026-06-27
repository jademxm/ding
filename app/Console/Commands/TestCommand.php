<?php

namespace App\Console\Commands;

use App\Services\DingTalkService;
use App\Services\EastMoneyStockService;
use App\Services\SinaStockService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $date = Carbon::now()->format('m-d H:i');
        $ding = new DingTalkService();
        $content = <<<TEXT
my_ding
💰 现价：10.00
🌐 幅度：Production
🕐 时间： {$date}
TEXT;
//        $resp = $ding->sendText($content, ['13166276989']);
//        $resp = $ding->sendMarkdown(
//            '🚀 部署通知',
//            "### Laravel 项目部署成功\n\n" .
//            "- 环境：**Local**\n" .
//            "- 分支：`main`\n" .
//            "- 部署人：张三\n" .
//            "- 时间：" . now(),
//            ['13166276989']
//        );
//        $resp = $ding->sendMarkdown(
//            '🎬 视频通知',
//            "### 有新视频待查看\n\n点击查看 → [播放视频](https://your-domain.com/video/123.mp4)"
//        );

        $sina = new SinaStockService();
        $resp = $sina->getQuote('000630');
        dd($resp);
    }
}
