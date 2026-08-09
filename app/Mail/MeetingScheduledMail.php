<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Meeting\Entities\Meeting;

class MeetingScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $meeting;
    public $recipientName;
    public $otherPartyName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Meeting $meeting, $recipientName, $otherPartyName)
    {
        $this->meeting = $meeting;
        $this->recipientName = $recipientName;
        $this->otherPartyName = $otherPartyName;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: __('Meeting Scheduled: ') . $this->meeting->title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.meeting-scheduled',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
