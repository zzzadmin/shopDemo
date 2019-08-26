<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //油价变动
        $redis = new \Redis();
        $redis->connect('127.0.0.1','6379');

        $schedule->call(function (Wechat $wechat,$redis) {
            file_put_contents(public_path().'/2.txt',1,FILE_APPEND);
            return ;
            //业务逻辑
        $price_info = file_get_contents('http://www.zuohaitao.cn/price/api');
        $price_arr = json_decode($price_info,1);
        //dd($price_arr);
        foreach($price_arr['result'] as $v){
            if($redis->exists($v['city'].'信息')){
                $redis_info = json_decode($redis->get($v['city'].'信息'),1);

                //dd($redis_info);
                foreach($v as $k=>$vv){

                    if($vv != $redis_info[$k]){
                        //推送模板消息
                        $openid_info = $wechat->app->user->list($nextOpenId = null);
                        $openid_list = $openid_info['data'];
                        //dd($openid_list);
                        foreach ($openid_list['openid'] as $vo){
                            $wechat->app->template_message->send([
                                'touser' => $vo,
                                'template_id' => '5orHUWxTQDVOwIJPURreYpglaPI6sZgBwAEqeUdAnWM',
                                'url' => 'http://www.zuohaitao.cn/price/api',
                                'data' => [
                                    'first' => '您好',
                                    'keyword1' => '今日油价有变动',
                                ],
                            ]);
                        }
                    }
                }
            }
        }
        //        $schedule->call(function () {
        //            // 业务逻辑
        //            \Log::Info('进入调度了！');
        })->everyMinute();

    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
