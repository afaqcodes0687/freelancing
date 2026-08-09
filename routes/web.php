<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Frontend\CategoryJobController;
use App\Http\Controllers\Frontend\CategoryProjectController;
use App\Http\Controllers\Frontend\FormController;
use App\Http\Controllers\Frontend\FreelancerListController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\FrontendHomeController;
use App\Http\Controllers\Frontend\FrontendJobsController;
use App\Http\Controllers\Frontend\FrontendProjectsController;
use App\Http\Controllers\Frontend\FrontendSubscriptionController;
use App\Http\Controllers\Frontend\JobDetailsController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\OrderIPNController;
use App\Http\Controllers\Frontend\ProfileDetailsController;
use App\Http\Controllers\Frontend\ProjectDetailsController;
use App\Http\Controllers\Frontend\SkillJobController;
use App\Http\Controllers\Frontend\SocialLoginController;
use App\Http\Controllers\Frontend\SubcategoryJobController;
use App\Http\Controllers\Frontend\ClientBenefitController;
use App\Http\Controllers\Frontend\TalentBenefitController;
use App\Http\Controllers\Frontend\SubcategoryProjectController;
use App\Http\Controllers\LiveSeederController;
use App\Http\Controllers\ManualMigrationController;
use App\Http\Controllers\LiveMigrationController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AffiliateLoginController;
use App\Http\Controllers\ReferralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\Route;

require_once __DIR__ . '/client.php';
require_once __DIR__ . '/freelancer.php';
require_once __DIR__ . '/affiliate.php';
require_once __DIR__ . '/admin.php';

//Route::get('test', function () {
//    return view('mail.order-mail', ['order_id' => 1, 'type' => 'freelancer']);
//});



Route::get('/sitemap.xml', [App\Http\Controllers\Frontend\SitemapController::class, 'index'])->name('sitemap');
// frontend starts
Route::get('/live-migrate-status-column', [LiveMigrationController::class, 'updateUserSubscriptionsStatusColumn']);
Route::get('/manual-migrate-project-columns', [ManualMigrationController::class, 'run']);

Route::get('/run-contact-form-seeder', [LiveSeederController::class, 'updateForm']);
Route::get('/create-bank-accounts-table', [AdminUserController::class, 'createBankAccountsTable']);
Route::group(['middleware' => ['globalVariable', 'maintains_mode', 'setlang', 'check.subscription']], function () {

    // public routes for user and admin
    Route::controller(AdminUserController::class)->group(function () {
        Route::post('get-state', 'get_country_state')->name('au.state.all');
        Route::post('get-city', 'get_state_city')->name('au.city.all');
        Route::post('get-subcategory', 'get_subcategory')->name('au.subcategory.all');
        Route::post('get-childcategory', 'get_childcategory')->name('au.childcategory.all');
    });

    // Fiverr-like aliases for public affiliate entry points
    Route::get('/affiliates/signup', function () {
        return redirect()->route('affiliate.register');
    })->name('affiliates.signup');

    Route::get('/affiliates/login', function () {
        return redirect()->route('affiliate.login');
    })->name('affiliates.login');

    // PayPro Webhook Route for Promotion Payments
    Route::post('/promote/paypro/webhook', [\Modules\PromoteFreelancer\Http\Controllers\Freelancer\BuyPromotePackageIPNController::class, 'handleWebhook'])
        ->name('promote.paypro.webhook')
        ->withoutMiddleware(['web', 'auth', 'verified', 'userEmailVerify', 'Google2FA', 'globalVariable', 'maintains_mode', 'setlang', 'check.subscription']);

    // Promotion Payment Success/Cancel Routes
    Route::get('/promote/payment/success/{id}', [\Modules\PromoteFreelancer\Http\Controllers\Freelancer\BuyPromotePackageController::class, 'paymentSuccess'])
        ->name('promote.payment.success');

    Route::get('/promote/payment/cancel/{id}', [\Modules\PromoteFreelancer\Http\Controllers\Freelancer\BuyPromotePackageController::class, 'paymentCancel'])
        ->name('promote.payment.cancel');

    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        return 'Cache cleared successfully';
    });

    // user registration
    Route::controller(RegisterController::class)->group(function () {
        Route::post('user-name-availability', 'userNameAvailability')->name('user.name.availability');
        Route::post('email-availability', 'emailAvailability')->name('user.email.availability');
        Route::post('phone-number-availability', 'phoneNumberAvailability')->name('user.phone.number.availability');
        // Route::match(['get','post'],'register','userRegister')->name('user.register');

        // Route::get('/register', function (Request $request) {
        //     if ($request->has('ref')) {
        //         session(['referral_code' => $request->get('ref')]);
        //     }
        //     return redirect()->route('user.register');
        // });

        Route::match(['get', 'post'], '/register', [RegisterController::class, 'userRegister'])->name('user.register')->middleware('throttle:5,1');
        ;
        Route::match(['get', 'post'], 'email-verify', 'emailVerify')->name('email.verify')->middleware('auth:web');
        Route::get('resend-verify-code-again', 'resendCode')->name('resend.verify.code')->middleware('auth:web');
        Route::post('password-validation', 'passwordValidation')->name('user.password.validation');
        Route::post('password-match-validation', 'passwordMatchValidation')->name('user.password.match.validation');

    });

    Route::controller(AffiliateController::class)->group(function () {


        // In your web.php (anywhere above affiliate/register route)
        Route::get('/affiliate/ref/{referral_code}', function ($referral_code) {
            $referrer = \App\Models\AffiliateProgram::where('referral_code', $referral_code)->first();

            $redirect = redirect()->route('affiliate.register');

            if ($referrer) {
                // set session for immediate attribution
                session(['referral_affiliate_id' => $referrer->id]);

                // set persistent cookie for 90 days
                $minutes = 60 * 24 * 90;
                return $redirect->withCookie(cookie('ref_aid', (string) $referrer->id, $minutes));
            }

            return $redirect;
        })->name('affiliate.referral.link');



        Route::match(['get', 'post'], 'affiliate/register', 'register')->name('affiliate.register');
        Route::match(['get', 'post'], 'affiliate/email-verify', 'emailVerify')->name('affiliate.email.verify');
        // Avoid duplicate name with user resend route; use affiliate-specific name
        Route::get('/affiliate/resend-code', [AffiliateController::class, 'resendCode'])->name('affiliate.resend.verify.code');
        Route::post('affiliate-user-name-availability', 'userNameAvailability')->name('affiliate.user.name.availability');
        Route::post('affiliate-email-availability', 'emailAvailability')->name('affiliate.user.email.availability');
        Route::post('affiliate-phone-number-availability', 'phoneNumberAvailability')->name('affiliate.user.phone.number.availability');
        Route::get('/affiliate/approve-commission/{token}', [AffiliateController::class, 'approveCommission'])->name('affiliate.approve.commission');

    });

    Route::controller(AffiliateLoginController::class)->group(function () {
        Route::match(['get', 'post'], 'affiliate/login', 'login')->name('affiliate.login');
    });

    // Referral routes
    Route::controller(ReferralController::class)->group(function () {
        Route::get('referral-program', 'index')->name('referral.program');
        Route::post('referral/send-invitation', 'sendInvitations')->name('referral.send.invitation');
        Route::post('referral/process', 'processReferral')->name('referral.process');
        Route::post('referral/complete', 'completeReferral')->name('referral.complete');
        Route::get('referral/stats', 'getStats')->name('referral.stats');
        Route::get('referral/list', 'getReferrals')->name('referral.list');
    });

    // user login
    Route::controller(LoginController::class)->group(function () {
        Route::match(['get', 'post'], 'login', 'userLogin')->name('user.login');
        Route::match(['get', 'post'], 'forget-password', 'forgetPassword')->name('user.forgot.password');
        Route::match(['get', 'post'], 'password-reset-otp', 'passwordResetOtp')->name('user.forgot.password.otp');
        Route::match(['get', 'post'], 'password-reset', 'passwordReset')->name('user.forgot.password.reset');
        Route::get('/payment/{subscription_id}', 'paymentIndex')->name('payment.index');
        Route::post('/payment/process', 'paymentProcess')->name('payment.process');
        Route::post('reset-password-validation', 'reset_passwordValidation')->name('user.reset.password.validation');
        Route::post('reset-password-match-validation', 'reset_passwordMatchValidation')->name('user.reset.password.match.validation');
    });

    // user social login
    Route::controller(SocialLoginController::class)->group(function () {
        Route::get('facebook/callback', 'facebook_callback')->name('facebook.callback');
        Route::get('facebook/redirect', 'facebook_redirect')->name('login.facebook.redirect');
        Route::get('auth/google/callback', 'google_callback')->name('google.callback');  // ← change
        Route::get('auth/google/redirect', 'google_redirect')->name('login.google.redirect');  // ← change
    });

    //freelancer list
    Route::controller(FreelancerListController::class)->group(function () {
        Route::get('talents', 'talents')->name('talents.all');
        Route::get('talents/all/filter', 'talents_filter')->name('talents.filter');
        Route::get('talents/all/pagination', 'pagination')->name('talents.pagination');
        Route::get('talents/filter/reset', 'reset')->name('talents.filter.reset');
        Route::get('/get-skills-by-category', 'getSkills')->name('get.skills.by.category');

    });

    Route::group(['middleware' => 'preventprojecturl'], function () {
        // all projects
        Route::controller(FrontendProjectsController::class)->group(function () {
            Route::get('projects', 'projects')->name('projects.all');
            Route::get('projects/all/pro', 'pro_projects')->name('pro.projects.all');
            Route::get('projects/all/filter', 'projects_filter')->name('projects.filter');
            Route::get('projects/all/pagination', 'pagination')->name('projects.pagination');
            Route::get('projects/filter/reset', 'reset')->name('projects.filter.reset');
        });
        // category projects
        Route::controller(CategoryProjectController::class)->group(function () {
            Route::get('categories/{slug}', 'category_projects')->name('category.projects');
            Route::get('categories/projects/filter', 'category_project_filter')->name('category.projects.filter');
            Route::get('categories/project/pagination', 'pagination')->name('category.project.pagination');
            Route::get('categories/project/filter/reset', 'reset')->name('category.project.filter.reset');
        });
        // subcategory projects
        Route::controller(SubcategoryProjectController::class)->group(function () {
            // Static routes MUST come before dynamic {slug} / {sub_slug}/{child_slug} patterns
            Route::get('sub-categories/projects/filter', 'sub_category_project_filter')->name('subcategory.projects.filter');
            Route::get('sub-categories/project/pagination', 'pagination')->name('subcategory.project.pagination');
            Route::get('sub-categories/project/filter/reset', 'reset')->name('subcategory.project.filter.reset');
            
            // Legacy redirects
            Route::get('sub-categories/{slug}', 'legacy_subcategory_redirect');
            Route::get('sub-categories/{sub_slug}/{child_slug}', 'legacy_child_redirect');

            // Dynamic routes
            Route::get('categories/{category_slug}/{sub_slug}', 'sub_category_projects')->name('subcategory.landing');
            Route::get('categories/{category_slug}/{sub_slug}/{child_slug}', 'child_category_projects')->name('child_category.landing');
        });
    });

    Route::group(['middleware' => 'preventjoburl'], function () {
        // jobs
        Route::controller(FrontendJobsController::class)->group(function () {
            Route::get('jobs', 'jobs')->name('jobs.all');
            Route::get('jobs/all', 'jobs')->name('jobs.all');
            Route::get('jobs/all/filter', 'jobs_filter')->name('jobs.filter');
            Route::get('jobs/all/pagination', 'pagination')->name('jobs.pagination');
            Route::get('jobs/filter/reset', 'reset')->name('jobs.filter.reset');
        });
        // category jobs
        Route::controller(CategoryJobController::class)->group(function () {
            Route::get('jobs/categories/pagination', 'pagination')->name('category.jobs.pagination');
            Route::get('jobs/categories/filter', 'category_jobs_filter')->name('category.jobs.filter');
            Route::get('jobs/categories/filter/reset', 'reset')->name('category.jobs.filter.reset');
            Route::get('jobs/categories/{slug}', 'category_jobs')->name('category.jobs');
        });
        // subcategory jobs
        Route::controller(SubcategoryJobController::class)->group(function () {
            Route::get('jobs/subcategories/pagination', 'pagination')->name('subcategory.jobs.pagination');
            Route::get('jobs/subcategories/filter', 'subcategory_jobs_filter')->name('subcategory.jobs.filter');
            Route::get('jobs/subcategories/filter/reset', 'reset')->name('subcategory.jobs.filter.reset');
            Route::get('jobs/subcategories/{slug}', 'subcategory_jobs')->name('subcategory.jobs');
        });
        // skill jobs
        Route::controller(SkillJobController::class)->group(function () {
            Route::get('jobs/skill/pagination', 'pagination')->name('skill.jobs.pagination');
            Route::get('jobs/skill/filter', 'skill_jobs_filter')->name('skill.jobs.filter');
            Route::get('jobs/skill/filter/reset', 'reset')->name('skill.jobs.filter.reset');
            Route::get('jobs/skill/{name?}', 'skill_jobs')->name('skill.jobs');
        });
    });

    // home job search
    Route::controller(FrontendHomeController::class)->group(function () {
        Route::get('job/project/search/from/home/page', 'project_or_job_search')->name('home.job.project.search');
    });


    //orders
    Route::controller(OrderController::class)->group(function () {
        Route::post('login/to/continue/order', 'user_login')->name('order.user.login');
        Route::post('order/user/confirm', 'user_order_confirm')->name('order.user.confirm');
        Route::get('order/success/page/{id}', 'user_order_success_page')->name('order.user.success.page');
        Route::get('order/payment/cancel/static', 'order_payment_cancel_static')->name('order.payment.cancel.static');
    });

    //order ipns
    Route::group(['prefix' => 'order', 'as' => 'pro.'], function () {
        Route::controller(OrderIPNController::class)->group(function () {
            Route::get('paypal-ipn', 'paypal_ipn_for_order')->name('paypal.ipn.order');
            Route::post('paytm-ipn', 'paytm_ipn_for_order')->name('paytm.ipn.order');
            //            Route::get('paystack-ipn','paystack_ipn_for_order')->name('paystack.ipn.order');
            Route::get('mollie/ipn', 'mollie_ipn_for_order')->name('mollie.ipn.order');
            Route::get('stripe/ipn', 'stripe_ipn_for_order')->name('stripe.ipn.order');
            Route::post('razorpay-ipn', 'razorpay_ipn_for_order')->name('razorpay.ipn.order');
            Route::get('flutterwave/ipn', 'flutterwave_ipn_for_order')->name('flutterwave.ipn.order');
            Route::get('midtrans-ipn', 'midtrans_ipn_for_order')->name('midtrans.ipn.order');
            Route::get('payfast-ipn', 'payfast_ipn_for_order')->name('payfast.ipn.order');
            Route::post('cashfree-ipn', 'cashfree_ipn_for_order')->name('cashfree.ipn.order');
            Route::get('instamojo-ipn', 'instamojo_ipn_for_order')->name('instamojo.ipn.order');
            Route::get('marcadopago-ipn', 'marcadopago_ipn_for_order')->name('marcadopago.ipn.order');
            Route::get('squareup-ipn', 'squareup_ipn_for_order')->name('squareup.ipn.order');
            Route::post('cinetpay-ipn', 'cinetpay_ipn_for_order')->name('cinetpay.ipn.order');
            Route::post('paytabs-ipn', 'paytabs_ipn_for_order')->name('paytabs.ipn.order');
            Route::post('billplz-ipn', 'billplz_ipn_for_order')->name('billplz.ipn.order');
            Route::post('zitopay-ipn', 'zitopay_ipn_for_order')->name('zitopay.ipn.order');
            Route::post('toyyibpay-ipn', 'toyyibpay_ipn_for_order')->name('toyyibpay.ipn.order');
            Route::get('authorize-ipn', 'authorizenet_ipn_for_order')->name('authorize.ipn.order');
            Route::post('pagali-ipn', 'pagali_ipn_for_order')->name('pagali.ipn.order');
            Route::post('siteways-ipn', 'siteways_ipn_for_order')->name('siteways.ipn.order');
            Route::post('iyzipay-ipn', 'iyzipay_ipn_for_order')->name('iyzipay.ipn.order');
            Route::post('kineticpay-ipn', 'kineticpay_ipn_for_order')->name('kineticpay.ipn.order');
            Route::post('awdpay-ipn', 'awdpay_ipn_for_order')->name('awdpay.ipn.order');
            Route::get('paypro-return', 'paypro_return_for_order')->name('paypro.return');
            Route::post('paypro-webhook', 'paypro_webhook_for_order')->name('paypro.webhook');
        });
    });

    // Fallback/Alias details for PayPro return to handle /paypro/return path
    Route::get('paypro/return', [\App\Http\Controllers\Frontend\OrderIPNController::class, 'paypro_return_for_order'])->name('paypro.return.fallback');


    // frontend custom form builders
    Route::controller(FormController::class)->group(function () {
        Route::post('form/custom-form/submit', 'custom_form_submit')->name('custom.form.submit');
    });

    Route::group(['middleware' => 'preventjoburl'], function () {
        //job details
        Route::controller(JobDetailsController::class)->group(function () {
            Route::get('jobs/{username}/{slug}', 'job_details')->name('job.details');
            Route::post('jobs/proposal/send/to-client', 'job_proposal_send')->name('job.proposal.send');
        });
    });

    Route::group(['middleware' => 'preventprojecturl'], function () {
        //project details
        Route::controller(ProjectDetailsController::class)->group(function () {
            Route::get('projects/{username}/{slug}', 'project_details')->name('project.details');
            Route::get('/project/review/load/more/data', 'load_more_review')->name('project.review.load.more');
        });
    });


    // dynamic single page
    Route::controller(FrontendController::class)->group(function () {
        Route::get('/home-page-three', 'home_page_3');
        Route::get('/', 'home_page')->name('homepage');
        Route::view('/how-it-works', 'frontend.pages.how-it-works')->name('how-it-works');
        Route::view('/about-us', 'frontend.pages.about-us')->name('about-us');
        Route::view('/iso-certificate', 'frontend.pages.certificate')->name('iso-certificate');
        Route::view('/secp-certificate', 'frontend.pages.secp-certificate')->name('secp-certificate');
        Route::view('/contact-us', 'frontend.pages.contact-us')->name('contact-us');
        Route::get('/investor-relations', 'investor_relations')->name('investor-relations');
        Route::view('/imprint', 'frontend.pages.imprint')->name('imprint');
        Route::get('/privacy-policy', function () {
            $policy = \App\Models\PrivacyPolicy::first();
            return view('frontend.pages.privacy-policy', compact('policy'));
        })->name('privacy-policy');
        Route::view('/support', 'frontend.pages.support')->name('support');
        Route::get('/terms-of-service', function () {
            $policy = \App\Models\TermsOfService::first();
            return view('frontend.pages.terms-of-service', compact('policy'));
        })->name('terms-of-service');
        Route::view('/terms-and-conditions', 'frontend.pages.terms-and-conditions')->name('terms-and-conditions');
        Route::view('/trust-and-safety', 'frontend.pages.trust-and-safety')->name('trust-and-safety');
        Route::get('/fees-and-charge', function () {
            $policy = \App\Models\FeesAndCharge::first();
            return view('frontend.pages.fees-and-charge', compact('policy'));
        })->name('fees-and-charge');
        Route::get('/service-and-shipping-policy', function () {
            $policy = \App\Models\ServiceShippingPolicy::bootstrap();
            return view('frontend.pages.service-and-shipping-policy', compact('policy'));
        })->name('service-shipping-policy');
        Route::get('/refund-and-return-policy', function () {
            $policy = \App\Models\RefundReturnPolicy::bootstrap();
            return view('frontend.pages.refund-and-return-policy', compact('policy'));
        })->name('refund-return-policy');
        Route::get('/affiliate-program', [App\Http\Controllers\Backend\AffiliateProgrammeController::class, 'frontend'])->name('affiliate-programme');
        Route::view('/win-work-with-rewards', 'frontend.pages.win-work-with-rewards')->name('win-work-with-rewards');
        Route::view('/ad/new', 'frontend.pages.ad')->name('ad.create');
        Route::post('/ad/new', 'adNew')->name('ad.store');
        Route::put('/ad/update', 'adUpdate')->name('ad.update');
        Route::delete('delete/{id}', 'destroy')->name('ad.delete');

        Route::view('contact-us', 'frontend.pages.contact-us')->name('frontend.dynamic.page');
        Route::get('dynamic/{slug}', 'dynamic_single_page')->name('frontend.dynamic.page');

        Route::view('/escrow-policy', 'frontend.pages.escrow-policy')->name('escrow-policy');
        Route::get('/partnership', 'partnership')->name('partnership');
        Route::get('client-benefits', [ClientBenefitController::class, 'index']);


        // Route::view('/client-benefits', 'frontend.pages.client-benefits')->name('client-benefits');
        Route::get('/talent-benefits', [TalentBenefitController::class, 'index'])->name('talent-benefits');

        Route::get('/referral-program', [App\Http\Controllers\Frontend\ReferralController::class, 'index'])->name('referral_program');
        Route::post('/referral-program/send-invitation', [App\Http\Controllers\Frontend\ReferralController::class, 'sendInvitation'])->name('referral.send.invitation');
        Route::get('/referral-program/test-mail', [App\Http\Controllers\Frontend\ReferralController::class, 'testMail'])->name('referral.test.mail');

      
        // Bio Page Routes
        Route::controller(\App\Http\Controllers\Frontend\BioController::class)->group(function () {
            Route::get('/u/{username}', 'show')->name('bio.show');
            Route::get('/u/{username}/link/{linkId}', 'redirectLink')->name('bio.link.redirect');
            Route::get('/u/{username}/qr/{format?}', 'downloadQrCode')->name('bio.qr.download');
            Route::get('/u/{username}/analytics', 'getAnalytics')->name('bio.analytics');
        });

    });

    // freelancer public profile view
    Route::controller(ProfileDetailsController::class)->group(function () {
        Route::get('profile-social-image/{username}', 'social_image')->name('freelancer.profile.social.image');
        Route::get('{username}', 'profile_details')->name('freelancer.profile.details');
        Route::post('freelancer/portfolio-details/display', 'portfolio_details')->name('freelancer.portfolio.details');
    });

    Route::get('/change-currency/{currency}', function ($currency) {
        if (in_array($currency, ['USD', 'PKR'])) {
            session()->put('active_currency', $currency);
            session()->put('manual_currency_selected', true);
            cookie()->queue(cookie()->forever('active_currency', $currency));
            cookie()->queue(cookie()->forever('manual_currency_selected', '1'));
        }
        return redirect()->back();
    })->name('change.currency');

});

