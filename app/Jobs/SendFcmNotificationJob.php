<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendFcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationBody;
    protected $toToken;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationBody, $toToken)
    {
        $this->notificationBody = $notificationBody;
        $this->toToken = $toToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->toToken)) {
            return;
        }

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'key=' . get_static_option('firebase_server_key'),
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'message' => [
                'body' => 'subject',
                'title' => 'title',
            ],
            'priority' => 'high',
            'data' => $this->notificationBody,
            'to' => $this->toToken,
        ]);
    }
}
