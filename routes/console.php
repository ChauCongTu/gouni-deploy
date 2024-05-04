<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('route:cache')->everySecond();
Schedule::command('app:handle-arena')->everyMinute();
Schedule::command('daily_stats')->daily();
