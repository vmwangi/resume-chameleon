<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyOnboardingReport extends Mailable
{
    use Queueable, SerializesModels;

    public $totalResponses;
    public $objectiveCounts;
    public $challengeCounts;
    public $referralSourceCounts;
    public $responses;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct(
        int $totalResponses,
        Collection $objectiveCounts,
        Collection $challengeCounts,
        Collection $referralSourceCounts,
        Collection $responses,
        string $date
    ) {
        $this->totalResponses = $totalResponses;
        $this->objectiveCounts = $objectiveCounts;
        $this->challengeCounts = $challengeCounts;
        $this->referralSourceCounts = $referralSourceCounts;
        $this->responses = $responses;
        $this->date = $date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Daily Onboarding Report - {$this->date}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.onboarding-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}