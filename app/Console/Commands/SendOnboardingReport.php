<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OnboardingResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyOnboardingReport;
use Carbon\Carbon;

class SendOnboardingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:onboarding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily onboarding responses report to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get yesterday's responses
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();
        
        $responses = OnboardingResponse::whereBetween('created_at', [$yesterday . ' 00:00:00', $today . ' 00:00:00'])
            ->with('user')
            ->get();
            
        // Get aggregate data
        $totalResponses = $responses->count();
        
        if ($totalResponses === 0) {
            $this->info('No onboarding responses from yesterday. Skipping email.');
            return 0;
        }
        
        // Objective stats
        $objectiveCounts = $responses->groupBy('objective')
            ->map(function ($group) {
                return $group->count();
            });
            
        // Challenge stats
        $challengeCounts = $responses->groupBy('challenge')
            ->map(function ($group) {
                return $group->count();
            });
            
        // Referral source stats
        $referralSourceCounts = $responses->groupBy('referral_source')
            ->map(function ($group) {
                return $group->count();
            });
        
        // Send email to admin
        Mail::to(config('mail.admin_address', 'admin@example.com'))
            ->send(new DailyOnboardingReport(
                $totalResponses,
                $objectiveCounts,
                $challengeCounts,
                $referralSourceCounts,
                $responses,
                $yesterday
            ));
            
        $this->info('Onboarding report sent successfully!');
        return 0;
    }
}