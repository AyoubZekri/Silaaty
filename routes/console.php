<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('users:check-experiments')->dailyAt("08:00");
Schedule::command('users:notify-trial')->dailyAt("08:00");
Schedule::command('notify:payment-day')->everyMinute();

