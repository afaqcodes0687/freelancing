<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationDetailsMissingMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public function __construct($args)
    {
        $this->data = $args;
    }

    public function build()
    {
        $mail = $this->from(get_static_option('site_global_email'), get_static_option('site_title'))
            ->subject($this->data['subject'])
            ->view('mail.verification-details-missing');

        // Attach screenshot if exists (as inline embedded image)
        if (isset($this->data['screenshot_base64']) && !empty($this->data['screenshot_base64'])) {
            $mail->attachData(
                base64_decode($this->data['screenshot_base64']), 
                $this->data['screenshot_name'] ?? 'screenshot.png', 
                [
                    'mime' => $this->data['screenshot_mime'] ?? 'image/png',
                    'Content-ID' => '<screenshot>',
                    'Content-Disposition' => 'inline; filename="' . ($this->data['screenshot_name'] ?? 'screenshot.png') . '"'
                ]
            );
        }

        return $mail;
    }
}
