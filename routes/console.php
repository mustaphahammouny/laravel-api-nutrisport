<?php

use App\Console\Commands\SendDailySalesReportCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendDailySalesReportCommand::class)->dailyAt('00:00');
