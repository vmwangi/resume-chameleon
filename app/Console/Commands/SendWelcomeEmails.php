<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmails extends Command
{
    protected $signature = 'send:welcome-emails';
    protected $description = 'Send welcome emails to users who signed up 12 hours ago.';

    public function handle()
    {
        $users = User::where('created_at', '<=', Carbon::now()->subHours(12))
                    ->where('welcome_email_sent', false)
                    ->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new WelcomeEmail($user));
            $user->welcome_email_sent = true;
            $user->save();
            $this->info("Welcome email sent to: {$user->email}");
        }

        $this->info('Welcome emails sent successfully.');
    }
}