<?php

namespace Modules\Meeting\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;
use Google_Service_Calendar_ConferenceSolutionKey;
use Modules\Meeting\Entities\UserGoogleAccount;
use Carbon\Carbon;

class GoogleMeetingService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        return $token;
    }

    protected function setToken($user = null)
    {
        // Try to get user-specific account first if user is provided
        if ($user) {
            $account = UserGoogleAccount::where('user_id', $user->id)->first();
        }

        // If no user account, return false (don't fallback to System Account anymore as per user request)
        if (empty($account)) {
            return false;
        }

        if (!$account) {
            return false;
        }

        $this->client->setAccessToken($account->access_token);

        if ($this->client->isAccessTokenExpired()) {
            if ($account->refresh_token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($account->refresh_token);
                if (isset($newToken['error'])) {
                    return false;
                }
                $account->update([
                    'access_token' => $newToken['access_token'],
                    'expires_in' => $newToken['expires_in'] ?? 3600,
                    'expires_at' => Carbon::now()->addSeconds($newToken['expires_in'] ?? 3600),
                ]);
                $this->client->setAccessToken($newToken);
            } else {
                return false;
            }
        }

        return true;
    }

    public function createMeeting($user, $details)
    {
        $preferredProvider = get_static_option('preferred_meeting_provider') ?? 'google';

        // Jitsi Meet Logic (Instant & Free)
        if ($preferredProvider == 'jitsi') {
            $roomName = 'rf-' . substr(md5(uniqid()), 0, 10);
            return [
                'event_id' => 'jitsi_' . $roomName,
                'meeting_link' => 'https://meet.jit.si/' . $roomName,
            ];
        }

        // Try to authenticate first to use Dynamic Links (Better for no-knocking)
        if (!$this->setToken($user)) {
            // Fallback to Static Link if account not connected
            $staticLink = get_static_option('static_meeting_link');
            if (!empty($staticLink)) {
                return [
                    'event_id' => 'static_' . uniqid(),
                    'meeting_link' => $staticLink,
                ];
            }
            return ['error' => 'Google account not connected (User or System) and no Static Link provided.'];
        }

        $service = new Google_Service_Calendar($this->client);

        $event = new Google_Service_Calendar_Event([
            'summary' => $details['title'],
            'description' => $details['description'] ?? '',
            'start' => [
                'dateTime' => Carbon::parse($details['start_time'])->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => Carbon::parse($details['end_time'])->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet'
                    ]
                ]
            ],
            'visibility' => 'public',
            'attendees' => array_filter([
                ['email' => $details['receiver_email']],
                !empty($details['sender_email']) ? ['email' => $details['sender_email']] : null,
            ]),
            'guestsCanInviteOthers' => true,
            'guestsCanSeeOtherGuests' => true,
            'reminders' => [
                'useDefault' => FALSE,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 10],
                ],
            ],
        ]);

        $calendarId = 'primary';
        $optParams = [
            'conferenceDataVersion' => 1,
            'sendUpdates' => 'all'
        ];
        
        try {
            $event = $service->events->insert($calendarId, $event, $optParams);
            return [
                'event_id' => $event->id,
                'meeting_link' => $event->hangoutLink,
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
