<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\FollowUpEmail;
use Illuminate\Support\Facades\Mail;

class SendFollowUpEmails extends Command
{
    protected $signature = 'send:follow-up-emails';
    protected $description = 'Send follow-up emails to users who signed up 7 days ago.';

    public function handle()
    {
        $users = User::where('created_at', '<=', Carbon::now()->subDays(7))
                    ->where('follow_up_email_sent', false)
                    ->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new FollowUpEmail($user));
            $user->follow_up_email_sent = true;
            $user->save();
            $this->info("Follow-up email sent to: {$user->email}");
        }

        $this->info('Follow-up emails sent successfully.');
    }
}