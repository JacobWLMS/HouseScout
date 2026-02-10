<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:cleanup-old-searches')->daily()->at('02:00');
Schedule::command('app:refresh-stale-data')->everySixHours();
