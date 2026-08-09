<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Meeting\Entities\Meeting;

class MeetingReminderMail extends Mailable
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
            subject: __('Reminder: Meeting with ') . $this->otherPartyName . __(' starting soon!'),
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
            view: 'mail.meeting-reminder',
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
