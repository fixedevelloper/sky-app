<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
/*Schedule::call(function () {
    Artisan::call('momo:status');
    info('Commande momo:status exécutée automatiquement.');
})->everyFiveMinutes();*/
Artisan::command('momo:status',function (){
    info('Commande momo:status exécutée automatiquement.');
})->everyMinute();
