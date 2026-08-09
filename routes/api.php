<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'v1', 'middleware' => 'setlang'], function () {

    // Currency Detection API (for Mobile App)
    Route::get('currency/detect', [\App\Http\Controllers\Api\CurrencyController::class, 'detect']);

    // Profanity Filter API (for Mobile App)
    Route::post('chat/check-profanity', [\App\Http\Controllers\Api\ProfanityController::class, 'checkProfanity']);

    // Privacy Policy API (for Mobile App)
    Route::controller(\App\Http\Controllers\Api\PrivacyPolicyController::class)->group(function () {
        Route::get('privacy-policy/app', 'getAppPrivacyPolicy');
        Route::get('privacy-policy/website', 'getWebsitePrivacyPolicy');
    });

    // Referral routes
    Route::controller(\App\Http\Controllers\ReferralController::class)->group(function () {
        Route::post('referral/process', 'processReferral');
        Route::post('referral/complete', 'completeReferral');
        Route::get('referral/stats', 'getReferralStats');
        Route::get('referral/history', 'getReferralHistory');
        Route::post('referral/send-invitations', 'sendInvitations');
        Route::post('referral/test-complete', 'testCompleteReferral'); // For testing purposes
    });

    // App update routes (global - no auth required)
    Route::controller(\App\Http\Controllers\Api\AppUpdateController::class)->group(function () {
        Route::post('app/check-update', 'checkUpdate');
        Route::get('app/download-url', 'getDownloadUrl');
        Route::post('app/record-installation', 'recordInstallation');
    });

    // Project Details API Routes
    Route::controller(\App\Http\Controllers\Api\ProjectDetailsController::class)->group(function () {
        Route::get('project-details/{username}/{slug}', 'project_details');
        Route::post('project-details/load-more-review', 'load_more_review');
        Route::post('order/confirm', 'order_confirm');
        Route::post('order/login', 'order_login');
        Route::get('order/success/page/{id}', 'order_success_page');
        Route::get('order/payment/cancel/static', 'order_payment_cancel_static');
        Route::post('profile/linked-accounts/update', 'profile_details_linked_accounts_update');
        Route::post('profile/linked-accounts/unlink', 'profile_details_linked_accounts_unlink');
        Route::get('order/paypro/callback', 'paypro_callback');
    });

    //freelancer route start
    Route::group(['prefix' => 'freelancer'], function () {

        // user registration
        Route::controller(\App\Http\Controllers\Api\Freelancer\RegisterController::class)->group(function () {
            Route::post('register', 'register');
            Route::post('resend-otp', 'resend_otp');
            Route::post('email-verify', 'email_verify');
            Route::post('username-availability', 'usernameAvailability');
            Route::post('email-availability', 'emailAvailability');
            Route::post('phone-availability', 'phoneAvailability');
        });

        // forget password
        Route::controller(\App\Http\Controllers\Api\Freelancer\ForgetPasswordController::class)->group(function () {
            Route::post('forget-password', 'forget_password');
            Route::post('confirm-email-by-otp', 'confirm_email_by_otp');
            Route::post('reset-password', 'reset_password');
        });

        // user login
        Route::controller(\App\Http\Controllers\Api\Freelancer\LoginController::class)->group(function () {
            Route::post('login', 'login');
        });

        //category manage
        Route::controller(\App\Http\Controllers\Api\Freelancer\CategoryManageController::class)->group(function () {
            Route::get('category/all', 'category');
        });

        //language
        Route::controller(\App\Http\Controllers\Api\Freelancer\LanguageController::class)->group(function () {
            Route::get('language/all', 'all_language');
            Route::post('language/string-translate', 'string_translate');
        });

        //job info
        Route::controller(\App\Http\Controllers\Api\Freelancer\JobController::class)->group(function () {
            Route::get('job/details/{id_or_slug?}', 'job_details');
            Route::post('job/filter', 'jobs_filter');
        });

        //frontend jobs
        Route::controller(\App\Http\Controllers\Api\FrontendJobsController::class)->group(function () {
            Route::get('jobs', 'index');
            Route::get('jobs/featured', 'featured');
            Route::get('jobs/search', 'search');
            Route::get('jobs/category/{categoryId}', 'getByCategory');
            Route::get('jobs/{id}', 'show');
        });

        //front subscription list
        Route::controller(\App\Http\Controllers\Api\Freelancer\SubscriptionController::class)->group(function () {
            Route::get('subscription/types', 'types');
            Route::post('subscription/list', 'all_front_subscription');
            Route::post('packages/buy', 'buy_subscription')->middleware('auth:sanctum');
        });

        //subscription API for Flutter
        Route::controller(\App\Http\Controllers\Api\SubscriptionApiController::class)->group(function () {
            Route::get('subscription/types/all', 'getSubscriptionTypes');
            Route::get('subscription/plans', 'getAllSubscriptions');
            Route::get('subscription/plan/{id}', 'getSubscriptionDetails');
            Route::get('subscription/current', 'getUserSubscription')->middleware('auth:sanctum');
            Route::get('subscription/history', 'getSubscriptionHistory')->middleware('auth:sanctum');
            Route::post('subscription/subscribe', 'subscribe')->middleware('auth:sanctum');
            Route::post('subscription/cancel', 'cancelSubscription')->middleware('auth:sanctum');
            Route::post('subscription/manual-activate', 'manualActivateSubscription')->middleware('auth:sanctum');
            Route::get('subscription/check-database', 'checkDatabaseSubscriptions')->middleware('auth:sanctum');
            Route::post('subscription/quick-activate', 'quickActivate')->middleware('auth:sanctum');
        });

        //PayPro return and webhook for subscription
        Route::controller(\App\Http\Controllers\Api\SubscriptionApiController::class)->group(function () {
            Route::get('subscription/paypro/return', 'payproReturn');
            Route::post('subscription/paypro/webhook', 'payproWebhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //social login
        Route::controller(\App\Http\Controllers\Api\Freelancer\SocialLoginController::class)->group(function () {
            Route::post('social/login', 'social_login');
        });

        //authenticated api
        Route::group(['middleware' => 'auth:sanctum'], function () {

            //logout
            Route::controller(\App\Http\Controllers\Api\Freelancer\LoginController::class)->group(function () {
                Route::post('logout', 'logout');
            });

            //country manage
            Route::controller(\App\Http\Controllers\Api\Freelancer\CountryManageController::class)->group(function () {
                Route::get('country/all', 'country');
                Route::post('state/all', 'state');
                Route::post('city/all', 'city');
            });

            //personal info
            Route::controller(\App\Http\Controllers\Api\Freelancer\PersonalInfoController::class)->group(function () {
                Route::get('personal/info', 'personal_info');
                Route::post('personal/info/update', 'personal_info_update');
                Route::post('profile/image/update', 'profile_image_update');
                Route::post('profile/password/update', 'change_password');
                Route::get('profile/details', 'profile_details');
                Route::post('account/delete', 'account_delete');
                Route::post('update/token', 'update_firebase_token');
            });

            //account setup
            Route::controller(\App\Http\Controllers\Api\Freelancer\AccountSetupController::class)->group(function () {
                Route::get('introduction/get', 'get_introduction');
                Route::post('introduction/add', 'add_introduction');
                Route::get('experience/all', 'get_experiences');
                Route::post('experience/add', 'add_experience');
                Route::post('experience/update', 'update_experience');
                Route::delete('experience/delete/{id}', 'delete_experience');
                Route::get('education/all', 'get_educations');
                Route::post('education/add', 'add_education');
                Route::post('education/update', 'update_education');
                Route::delete('education/delete/{id}', 'delete_education');
                Route::get('work/selected', 'get_selected_categories');
                Route::post('work/add', 'add_work');
                Route::get('skill/all', 'get_skills');
                Route::post('skill/add', 'add_skill');
                Route::get('skill/by-subcategory/{subcategory_id}', 'get_skills_by_subcategory');
                Route::get('hourly-rate/get', 'get_hourly_rate');
                Route::post('hourly-rate/update', 'update_hourly_rate');
                Route::get('setup/status', 'get_setup_status');
            });

            //projects
            Route::controller(\App\Http\Controllers\Api\Freelancer\ProjectController::class)->group(function () {
                Route::get('project/list', 'project_list');
                Route::post('project/create', 'create_project');
                Route::get('project/details/{id}', 'project_details');
                Route::post('project/update', 'update_project');
                Route::post('project/delete', 'delete_project');
                Route::post('project/availability', 'availability_status');
                Route::post('project/user-work-availability-status', 'work_availability_status');
            });

            //order info
            Route::controller(\App\Http\Controllers\Api\Freelancer\OrderController::class)->group(function () {
                Route::get('order/all', 'all_order');
                Route::get('order/details/{id?}', 'order_details');
                Route::post('order/accept', 'order_accept');
                Route::post('order/decline', 'order_decline');
                Route::post('order/submit', 'order_submit');
                Route::post('order/order-rating/{id}', 'order_rating');
            });

            //report info
            Route::controller(\App\Http\Controllers\Api\Freelancer\ReportController::class)->group(function () {
                Route::post('report', 'store');
                Route::get('reports', 'all');
                Route::get('report/{id}', 'show');
            });

            //job info
            Route::controller(\App\Http\Controllers\Api\Freelancer\JobController::class)->group(function () {
                Route::get('job/all', 'all_job');
                Route::get('job/my-proposals', 'my_proposal');
                Route::get('job/my-offers', 'my_offer');
                Route::post('job/proposal-send', 'job_proposal_send');
            });

        });

        // Game payment success route (no auth required for PayPro callback)
        Route::controller(\App\Http\Controllers\Api\OneRupeeGameController::class)->group(function () {
            Route::get('game/pay/success/{drawId}/{encodedInfo}', 'payproSuccess');
            Route::get('game/pay/success/{drawId}', 'payproSuccess'); // Fallback for old format
        });

            //support ticket
            Route::controller(\App\Http\Controllers\Api\Freelancer\TicketController::class)->group(function () {
                Route::get('department/all', 'all_department');
                Route::get('ticket/all', 'all_ticket');
                Route::get('ticket/single/all-message/{id?}', 'all_message');
                Route::post('ticket/create', 'create_ticket');
                Route::get('ticket/details/{id?}', 'ticket_details');
                Route::post('ticket/message-send', 'ticket_message_send');
            });

            //invoice management
            Route::controller(\App\Http\Controllers\Api\Freelancer\InvoiceController::class)->group(function () {
                Route::get('invoice/eligible-orders', 'eligible_orders');
                Route::get('invoice/generate/{order_id}', 'generate_invoice');
                Route::get('invoice/download/{order_id}', 'download_invoice');
                Route::get('invoice/info/{order_id}', 'invoice_info');
            });

            //new wallet api
            Route::controller(\App\Http\Controllers\Api\Freelancer\WalletApiController::class)->group(function () {
                Route::get('wallet/dashboard', 'get_wallet_dashboard');
                Route::get('wallet/history', 'get_wallet_history');
                Route::get('wallet/bank-info', 'get_bank_info');
                Route::post('wallet/bank-info', 'update_bank_info');
                Route::get('wallet/withdraw/settings', 'withdraw_settings');
                Route::post('wallet/withdraw/request', 'withdraw_request');
                Route::get('wallet/withdraw/history', 'get_withdrawal_history');
                
                // Deposit API routes
                Route::get('wallet/deposit/settings', 'get_deposit_settings');
                Route::post('wallet/deposit/request', 'deposit_request');
                Route::get('wallet/deposit/history', 'get_deposit_history');
            });

            //identity verification
            Route::controller(\App\Http\Controllers\Api\Freelancer\IdentityVerificationApiController::class)->group(function () {
                Route::get('identity-verification/status', 'get_verification_status');
                Route::post('identity-verification/submit', 'submit_verification');
                Route::get('identity-verification/countries', 'get_countries');
                Route::post('identity-verification/states', 'get_states');
                Route::post('identity-verification/cities', 'get_cities');
            });

            //wallet
            Route::controller(\App\Http\Controllers\Api\Freelancer\WalletController::class)->group(function () {
                Route::get('wallet/history', 'wallet_history');
                Route::post('wallet/deposit', 'deposit');
                Route::post('wallet/deposit/update-payment', 'payment_update');
            });

            //withdraw
            Route::controller(\App\Http\Controllers\Api\Freelancer\WithdrawController::class)->group(function () {
                Route::get('withdraw/settings', 'withdraw_settings');
                Route::post('withdraw/request', 'withdraw_request');
                Route::get('withdraw/history', 'withdraw_history');
            });

            //notification
            Route::controller(\App\Http\Controllers\Api\Freelancer\NotificationController::class)->group(function () {
                Route::get('notification/unread', 'unread_notification');
                Route::get('notification/unread/count', 'unread_notification_count');
                Route::post('notification/read', 'read_notification');
            });

            Route::controller(\App\Http\Controllers\Api\Freelancer\PaymentListController::class)->group(function () {
                Route::get('gateway/list', 'gateway_list');
            });

            //subscription list
            Route::controller(\App\Http\Controllers\Api\Freelancer\SubscriptionController::class)->group(function () {
                Route::get('subscription/history/list', 'all_subscription');
                Route::post('subscription/buy', 'buy_subscription');
                Route::post('subscription/buy/update-payment', 'payment_update');
            });

            //live chat
            Route::controller(\Modules\Chat\Http\Controllers\Api\Freelancer\OfferController::class)->group(function () {
                Route::post('offer/send', 'offer_send');
            });

            //live chat
            Route::controller(\Modules\Chat\Http\Controllers\Api\Freelancer\ChatController::class)->group(function () {
                Route::get('chat/client-list', 'client_list');
                Route::get('chat/fetch-record/{live_chat_id?}', 'fetch_record');
                Route::post('chat/message-send', 'message_send');
                Route::get('chat/credentials', 'credentials');
                Route::get('chat/unseen-message/count', 'unseen_message_count');
                Route::post('chat/message-delivered/{message_id}', 'message_delivered');
                Route::post('chat/message-seen/{message_id}', 'message_seen');
            });

            //chat favorite
            Route::controller(\Modules\Chat\Http\Controllers\Api\Freelancer\ChatFavoriteController::class)->group(function () {
                Route::post('chat-favorite/toggle', 'toggle');
                Route::get('chat-favorites', 'index');
                Route::get('chat-favorite/check', 'check');
            });

            //chat block
            Route::controller(\Modules\Chat\Http\Controllers\Api\Freelancer\ChatBlockController::class)->group(function () {
                Route::post('chat-block/toggle', 'toggle');
                Route::get('chat-block/check', 'check');
            });

        });

    //freelancer route end

    //client route start
    Route::group(['prefix' => 'client'], function () {

        // user registration
        Route::controller(\App\Http\Controllers\Api\Client\RegisterController::class)->group(function () {
            Route::post('register', 'register');
            Route::post('resend-otp', 'resend_otp');
            Route::post('email-verify', 'email_verify');
            Route::post('username-availability', 'usernameAvailability');
            Route::post('email-availability', 'emailAvailability');
            Route::post('phone-availability', 'phoneAvailability');
        });
        // forget password
        Route::controller(\App\Http\Controllers\Api\Client\ForgetPasswordController::class)->group(function () {
            Route::post('forget-password', 'forget_password');
            Route::post('confirm-email-by-otp', 'confirm_email_by_otp');
            Route::post('reset-password', 'reset_password');
        });
        // user login
        Route::controller(\App\Http\Controllers\Api\Client\LoginController::class)->group(function () {
            Route::post('login', 'login');
        });
        //category manage
        Route::controller(\App\Http\Controllers\Api\Client\CategoryManageController::class)->group(function () {
            Route::get('category/all', 'category');
        });
        Route::controller(\App\Http\Controllers\Api\Client\JobController::class)->group(function () {
            Route::get('skill/all', 'skill');
        });
        //language
        Route::controller(\App\Http\Controllers\Api\Client\LanguageController::class)->group(function () {
            Route::get('language/all', 'all_language');
            Route::post('language/string-translate', 'string_translate');
        });

        //project info
        Route::controller(\App\Http\Controllers\Api\Client\ProjectWithFilterController::class)->group(function () {
            Route::get('projects/all', 'projects');
            //            Route::get('projects/all/pro', 'pro_projects')->name('pro.projects.all');
            Route::post('projects/all/filter', 'projects_filter');
            Route::get('project/details/{id?}', 'project_details');
        });

        //profile details
        Route::controller(\App\Http\Controllers\Api\Client\ProfileDetailsController::class)->group(function () {
            Route::get('profile/details/{username?}', 'profile_details');
        });

        //frontend jobs
        Route::controller(\App\Http\Controllers\Api\FrontendJobsController::class)->group(function () {
            Route::get('jobs', 'index');
            Route::get('jobs/featured', 'featured');
            Route::get('jobs/search', 'search');
            Route::get('jobs/category/{categoryId}', 'getByCategory');
            Route::get('jobs/{id}', 'show');
        });

        //country manage
        Route::controller(\App\Http\Controllers\Api\Client\CountryManageController::class)->group(function () {
            Route::get('country/all', 'country');
            Route::post('state/all', 'state');
            Route::post('city/all', 'city');
        });

        //authenticated api
        Route::group(['middleware' => 'auth:sanctum'], function () {

            //logout
            Route::controller(\App\Http\Controllers\Api\Client\LoginController::class)->group(function () {
                Route::post('logout', 'logout');
            });
            //personal info
            Route::controller(\App\Http\Controllers\Api\Client\PersonalInfoController::class)->group(function () {
                Route::get('personal/info', 'personal_info');
                Route::post('personal/info/update', 'personal_info_update');
                Route::post('profile/image/update', 'profile_image_update');
                Route::post('profile/password/update', 'change_password');
                Route::post('account/delete', 'account_delete');
                Route::post('update/token', 'update_firebase_token');
            });
            //payment gateway list info
            Route::controller(\App\Http\Controllers\Api\Client\PaymentGatewayListController::class)->group(function () {
                Route::get('payment/gateway-list', 'payment_gateway_list');
            });
            //order info
            Route::controller(\App\Http\Controllers\Api\Client\OrderController::class)->group(function () {
                Route::post('order/confirm-order', 'user_order_confirm');
                Route::post('order/payment-update', 'payment_update');
                Route::get('order/all-order', 'all_order');
                Route::get('order/order-details/{id}', 'order_details');
                Route::post('order/request-revision', 'request_revision');
                Route::post('order/order-milestone-approve', 'order_milestone_approve');
                Route::post('order/order-rating/{id}', 'order_rating');
                Route::post('order/report', 'report');
            });

            //report info
            Route::controller(\App\Http\Controllers\Api\Client\ReportController::class)->group(function () {
                Route::post('report', 'store');
                Route::get('reports', 'all');
                Route::get('report/{id}', 'show');
            });
            //job info
            Route::controller(\App\Http\Controllers\Api\Client\JobController::class)->group(function () {
                Route::post('job/create', 'job_create');
                Route::post('job/edit', 'job_edit');
                Route::get('job/all', 'all_job');
                Route::get('job/details/{id?}', 'job_details');
                Route::post('job/proposals/filter', 'job_proposal_filter');
                Route::post('job/proposal/add-to-shortlist', 'add_remove_shortlist');
                Route::post('job/proposal/reject', 'reject_proposal');
                Route::post('job/open/close', 'open_close');
                Route::post('job/update/hourly-rate-hours', 'rate_and_hours');
                Route::post('job/get-skills-by-subcategory', 'getSkillsBySubcategory');
            });
            //my offer
            Route::controller(\App\Http\Controllers\Api\Client\OfferController::class)->group(function () {
                Route::get('offer/all', 'all_offers');
                Route::get('offer/details/{id?}', 'offer_details');
            });

            //support ticket
            Route::controller(\App\Http\Controllers\Api\Client\TicketController::class)->group(function () {
                Route::get('department/all', 'all_department');
                Route::get('ticket/all', 'all_ticket');
                Route::get('ticket/single/all-message/{id?}', 'all_message');
                Route::post('ticket/create', 'create_ticket');
                Route::get('ticket/details/{id?}', 'ticket_details');
                Route::post('ticket/message-send', 'ticket_message_send');
            });

            //wallet
            Route::controller(\App\Http\Controllers\Api\Client\WalletController::class)->group(function () {
                Route::get('wallet/history', 'wallet_history');
                Route::post('wallet/deposit', 'deposit');
                Route::post('wallet/deposit/update-payment', 'payment_update');
                
                // New deposit API routes
                Route::get('wallet/deposit/settings', 'get_deposit_settings');
                Route::get('wallet/deposit/history', 'get_deposit_history');
            });

            //notification
            Route::controller(\App\Http\Controllers\Api\Client\NotificationController::class)->group(function () {
                Route::get('notification/unread', 'unread_notification');
                Route::get('notification/unread/count', 'unread_notification_count');
                Route::post('notification/read', 'read_notification');
            });

            //identity verification
            Route::controller(\App\Http\Controllers\Api\Client\IdentityVerificationApiController::class)->group(function () {
                Route::get('identity-verification/status', 'get_verification_status');
                Route::post('identity-verification/submit', 'submit_verification');
                Route::get('identity-verification/countries', 'get_countries');
                Route::post('identity-verification/states', 'get_states');
                Route::post('identity-verification/cities', 'get_cities');
            });

            //live chat
            Route::controller(\Modules\Chat\Http\Controllers\Api\Client\ChatController::class)->group(function () {
                Route::get('chat/freelancer-list', 'freelancer_list');
                Route::get('chat/fetch-record/{live_chat_id?}', 'fetch_record');
        });

        //notification
        Route::controller(\App\Http\Controllers\Api\Client\NotificationController::class)->group(function () {
            Route::get('notification/unread', 'unread_notification');
            Route::get('notification/unread/count', 'unread_notification_count');
            Route::post('notification/read', 'read_notification');
        });

        //identity verification
        Route::controller(\App\Http\Controllers\Api\Client\IdentityVerificationApiController::class)->group(function () {
            Route::get('identity-verification/status', 'get_verification_status');
            Route::post('identity-verification/submit', 'submit_verification');
            Route::get('identity-verification/countries', 'get_countries');
            Route::post('identity-verification/states', 'get_states');
            Route::post('identity-verification/cities', 'get_cities');
        });

        //live chat
        Route::controller(\Modules\Chat\Http\Controllers\Api\Client\ChatController::class)->group(function () {
            Route::get('chat/freelancer-list', 'freelancer_list');
            Route::get('chat/fetch-record/{live_chat_id?}', 'fetch_record');
            Route::post('chat/message-send', 'message_send');
            Route::get('chat/credentials', 'credentials');
            Route::get('chat/unseen-message/count', 'unseen_message_count');
            Route::post('chat/message-delivered/{message_id}', 'message_delivered');
            Route::post('chat/message-seen/{message_id}', 'message_seen');
        });

        //chat favorite
        Route::controller(\Modules\Chat\Http\Controllers\Api\Client\ChatFavoriteController::class)->group(function () {
            Route::post('chat-favorite/toggle', 'toggle');
            Route::get('chat-favorites', 'index');
            Route::get('chat-favorite/check', 'check');
        });

        //chat block
        Route::controller(\Modules\Chat\Http\Controllers\Api\Client\ChatBlockController::class)->group(function () {
            Route::post('chat-block/toggle', 'toggle');
            Route::get('chat-block/check', 'check');
        });

    });

});

});

//client route end

// Game PayPro Payment Routes (no auth required for callbacks)
Route::prefix('game')->group(function () {
    Route::get('paypro/success', [\App\Http\Controllers\Api\OneRupeeGameController::class, 'payproSuccess']);
    Route::post('paypro/webhook', [\App\Http\Controllers\Api\OneRupeeGameController::class, 'payproWebhook'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});
