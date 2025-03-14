<?php

use Illuminate\Support\Facades\Schedule;
 
Schedule::command('report:onboarding')->dailyAt('08:00'); 
Schedule::command('send:welcome-emails')->hourly();
Schedule::command('send:follow-up-emails')->daily(); 