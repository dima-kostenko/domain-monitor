<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Domain availability checks
|--------------------------------------------------------------------------
|
| The scheduler runs every minute via: * * * * * php artisan schedule:run
|
| For each domain we want to respect its individual check_interval.
| The CheckDomains command itself filters domains whose next check is due,
| so running it every minute is cheap when there's nothing to do.
|
*/

Schedule::command('check:domains')
    ->everyMinute()
    ->withoutOverlapping(10)       // max lock time: 10 minutes
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/domain-checks.log'));
