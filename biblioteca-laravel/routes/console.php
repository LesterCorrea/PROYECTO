<?php

use Illuminate\Support\Facades\Schedule;

// Calcular multas vencidas cada noche a las 00:00
Schedule::command('biblioteca:calcular-multas')->dailyAt('00:00');


// use Illuminate\Foundation\Inspiring;
// use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote'); -->
