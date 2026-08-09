<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ReferralInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\ReferralInvitation;

class ReferralController extends Controller
{
    public function index()
    {
        return view('frontend.pages.referral_program');
    }


    public function sendInvitation(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'emails' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $emails = array_map('trim', explode(',', $request->emails));
        $emails = array_filter($emails);

        $successCount = 0;
        $duplicateEmails = [];
        $invalidEmails = [];

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalidEmails[] = $email;
                continue;
            }

            $alreadyInvited = ReferralInvitation::where('user_id', $user->id)
                ->where('email', $email)
                ->exists();

            if ($alreadyInvited) {
                $duplicateEmails[] = $email;
                continue;
            }

            try {
                // Send invite
                Mail::to($email)->send(new ReferralInvitationMail($user, $email));

                // Store invitation
                ReferralInvitation::create([
                    'user_id' => $user->id,
                    'email' => $email,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                \Log::error("Invite error: {$email} - " . $e->getMessage());
            }
        }

        // Build message
        $message = "{$successCount} invitation(s) sent.";

        if (!empty($duplicateEmails)) {
            $message .= " Already invited: " . implode(', ', $duplicateEmails) . ".";
        }

        if (!empty($invalidEmails)) {
            $message .= " Invalid emails: " . implode(', ', $invalidEmails) . ".";
        }

        if ($successCount > 0) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'sent_count' => $successCount,
                'already_sent' => $duplicateEmails,
                'invalid' => $invalidEmails
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No invitations sent. ' . 
                    (!empty($duplicateEmails) ? 'Already invited: ' . implode(', ', $duplicateEmails) . '. ' : '') .
                    (!empty($invalidEmails) ? 'Invalid emails: ' . implode(', ', $invalidEmails) . '.' : ''),
                'sent_count' => 0,
                'already_sent' => $duplicateEmails,
                'invalid' => $invalidEmails
            ]);
        }

    }

    /**
     * Test mail configuration
     */
    public function testMail()
    {
        try {
            Mail::raw('Test email from Right Freelancer', function($message) {
                $message->to('test@example.com')
                        ->subject('Mail Test')
                        ->from(get_static_option('site_global_email'), get_static_option('site_title'));
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Mail configuration is working correctly.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mail configuration error: ' . $e->getMessage()
            ]);
        }
    }
} 