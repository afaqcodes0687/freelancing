<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Meeting\Entities\Meeting;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingReminderMail;
use Carbon\Carbon;

class SendMeetingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meeting:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to participants before meeting starts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for upcoming meetings...');

        // Find meetings starting in the next 45 minutes that haven't been reminded yet
        $upcomingMeetings = Meeting::with(['sender', 'receiver'])
            ->where('status', 'scheduled')
            ->where('reminder_status', 0)
            ->where('start_time', '>', Carbon::now())
            ->where('start_time', '<=', Carbon::now()->addMinutes(45))
            ->get();

        if ($upcomingMeetings->isEmpty()) {
            $this->info('No upcoming meetings found for reminders.');
            return;
        }

        foreach ($upcomingMeetings as $meeting) {
            $this->info("Sending reminders for meeting: " . $meeting->title);

            try {
                // Send to Sender (Host)
                Mail::to($meeting->sender->email)->send(new MeetingReminderMail(
                    $meeting,
                    $meeting->sender->fullname ?? $meeting->sender->username,
                    $meeting->receiver->fullname ?? $meeting->receiver->username
                ));

                // Send to Receiver (Recipient)
                Mail::to($meeting->receiver->email)->send(new MeetingReminderMail(
                    $meeting,
                    $meeting->receiver->fullname ?? $meeting->receiver->username,
                    $meeting->sender->fullname ?? $meeting->sender->username
                ));

                // Mark as sent
                $meeting->update(['reminder_status' => 1]);
                
                $this->info("Reminders sent successfully for ID: " . $meeting->id);
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for ID: " . $meeting->id . " - Error: " . $e->getMessage());
            }
        }

        $this->info('Meeting reminder task completed.');
    }
}
