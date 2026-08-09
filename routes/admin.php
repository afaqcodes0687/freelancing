<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Backend\AccountSettingsController;
use App\Http\Controllers\Backend\AdditionalSettingsController;
use App\Http\Controllers\Backend\AdminNotificationController;
use App\Http\Controllers\Backend\AdminPasswordResetController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FeedbackController;
use App\Http\Controllers\Backend\InvoiceController;
use App\Http\Controllers\Backend\JobHistoryController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\LoginRegisterController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\PageBuilderController;
use App\Http\Controllers\Backend\ProjectController;
use App\Http\Controllers\Backend\ProjectHistoryController;
use App\Http\Controllers\Backend\JobController;
use App\Http\Controllers\Backend\SkillController;
use App\Http\Controllers\Backend\FormBuilderController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\SuspendActiveController;
use App\Http\Controllers\Backend\TransactionController;
use App\Http\Controllers\Backend\UserManageController;
use App\Http\Controllers\Backend\UserReportController;
use App\Http\Controllers\Backend\ReferralInvitationController;
use App\Http\Controllers\Backend\AffiliateCommissionController;
use App\Http\Controllers\Backend\AffiliatePayoutController;
use App\Http\Controllers\Backend\OneRupeeGameAdminController;
use App\Http\Controllers\Backend\WidgetBuilderController;
use App\Http\Controllers\Backend\AdminAffiliateSupportController;
use App\Http\Controllers\Backend\AdManageController;
use App\Http\Controllers\Backend\AppUpdateAdminController;
use App\Http\Controllers\Backend\ClientBenefitController;
use App\Http\Controllers\Backend\TalentBenefitController;
use App\Http\Controllers\Backend\AboutUsController;
use App\Http\Controllers\Backend\PrivacyPolicyController;
use App\Http\Controllers\Backend\TermsOfServiceController;
use App\Http\Controllers\Backend\ServiceShippingPolicyController;
use App\Http\Controllers\Backend\RefundReturnPolicyController;
use App\Http\Controllers\Backend\TrustAndSafetyController;
use App\Http\Controllers\Backend\SupportController;
use App\Http\Controllers\Backend\HowItWorksController;
use App\Http\Controllers\Backend\ReferralSettingsController;
use App\Http\Controllers\Backend\WinWorkWithRewardsController;
use App\Http\Controllers\Backend\AffiliateProgrammeController;
use App\Http\Controllers\Common\MediaUploadController;
use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Http\Controllers\AdminManageController;
use App\Http\Controllers\Admin\SeoSettingController;
Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {

    Route::match(['get', 'post'], '/', [LoginController::class, 'adminLogin'])->name('login');

    Route::controller(AdminPasswordResetController::class)->group(function () {
        Route::match(['get', 'post'], 'forget-password', 'forgetPassword')->name('forgot.password');
        Route::match(['get', 'post'], 'password-reset-otp', 'passwordResetOtp')->name('forgot.password.otp');
        Route::match(['get', 'post'], 'password-reset', 'passwordReset')->name('forgot.password.reset');
    });


    Route::group(['middleware' => ['auth:admin', 'setlang']], function () {
        Route::controller(LoginController::class)->group(function () {
            Route::get('account/logout', 'adminLogout')->name('logout');
        });

        // One Dollar Game (Admin)
        Route::group(['prefix' => 'one-rupee-game'], function () {
            Route::controller(OneRupeeGameAdminController::class)->group(function () {
                Route::get('draws', 'index')->name('one.rupee.game.index');
                Route::post('draws/schedule-today', 'scheduleToday')->name('one.rupee.game.schedule.today');
                Route::get('draws/{id}/entries', 'entries')->name('one.rupee.game.entries');
                Route::get('draws/{id}/edit', 'edit')->name('one.rupee.game.edit');
                Route::put('draws/{id}', 'update')->name('one.rupee.game.update');
                Route::delete('draws/{id}', 'destroy')->name('one.rupee.game.destroy');
                Route::post('draws/{id}/pick-winner', 'pickWinner')->name('one.rupee.game.pick.winner');
                Route::post('draws', 'store')->name('one.rupee.game.store');
                Route::post('draws/{id}/activate', 'activate')->name('one.rupee.game.activate');
                Route::post('draws/{id}/deactivate', 'deactivate')->name('one.rupee.game.deactivate');
                Route::get('settings', 'settings')->name('one.rupee.game.settings');
                Route::post('settings', 'updateSettings');
                Route::get('withdrawals', 'withdrawals')->name('one.rupee.game.withdrawals');
                Route::post('withdrawals/{id}/update', 'withdrawUpdate')->name('one.rupee.game.withdrawals.update');

            });
        });

        Route::prefix('app-update')->name('app.update.')->controller(AppUpdateAdminController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/settings', 'updateSettings')->name('settings');
            Route::get('/installations', 'installations')->name('installations');
            Route::post('/versions', 'store')->name('store');
            Route::get('/versions/{id}/edit', 'edit')->name('edit');
            Route::put('/versions/{id}', 'update')->name('update');
            Route::post('/versions/{id}/activate', 'activate')->name('activate');
            Route::delete('/versions/{id}', 'destroy')->name('destroy');
        });

        // dashboard
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
        });

        // project
        Route::controller(ProjectController::class)->group(function () {
            Route::group(['prefix' => 'project'], function () {
                Route::get('all', 'all_project')->name('project')->permission('project-list');
                Route::post('reject-project', 'reject_project')->name('project.reject')->permission('project-reject');
                Route::match(['get', 'post'], 'auto-approval-settings', 'auto_approval_settings')->name('project.approval.settings');
                Route::get('search-project', 'search_project')->name('project.search');
                Route::get('paginate/data', 'pagination')->name('project.paginate.data');
                Route::get('details/{id}', 'project_details')->name('project.details')->permission('project-details');
                Route::post('change-status/{id}', 'change_status')->name('project.status.change')->permission('project-status-change');
                Route::get('edit/{id}', 'edit_project')->name('project.edit')->permission('project-edit');
                Route::post('update/{id}', 'update_project')->name('project.update')->permission('project-edit');
                Route::post('delete/{id}', 'delete_project')->name('project.delete')->permission('project-delete');
            });
        });

        // project history
        Route::controller(ProjectHistoryController::class)->group(function () {
            Route::group(['prefix' => 'project-history'], function () {
                Route::get('all', 'all_history')->name('project.history')->permission('project-history-list');
                Route::get('search-project', 'search_history')->name('project.history.search');
                Route::get('paginate/data', 'pagination')->name('project.history.paginate.data');
            });
        });

        // notification
        Route::controller(AdminNotificationController::class)->group(function () {
            Route::group(['prefix' => 'notification'], function () {
                Route::get('all', 'all_notification')->name('notification.all')->permission('notification-list');
                Route::match(['get', 'post'], 'settings', 'notification_settings')->name('notification.settings')->permission('notification-list');
                Route::post('all/read', 'read_notification')->name('notification.read')->permission('notification-list');
                Route::get('search-notification', 'search_notification')->name('notification.search');
                Route::get('paginate/data', 'pagination')->name('notification.paginate.data');
            });
        });

        // Ad Management
        Route::controller(AdManageController::class)->group(function () {
            Route::group(['prefix' => 'ads'], function () {
                Route::get('all', 'index')->name('ads.all');
                Route::post('change-status', 'change_status')->name('ads.change.status');
                Route::post('update-stats', 'update_stats')->name('ads.update.stats');
                Route::post('delete/{id}', 'destroy')->name('ads.delete');
            });
        });

        // job
        Route::controller(JobController::class)->group(function () {
            Route::group(['prefix' => 'job'], function () {
                Route::get('all', 'all_job')->name('jobs')->permission('job-list');
                Route::match(['get', 'post'], 'auto-approval-settings', 'auto_approval_settings')->name('job.approval.settings')->permission('job-auto-approval');
                Route::get('search-job', 'search_job')->name('job.search');
                Route::get('paginate/data', 'pagination')->name('job.paginate.data');
                Route::get('details/{id}', 'job_details')->name('job.details')->permission('job-details');
                Route::post('change-status/{id}', 'change_status')->name('job.status.change')->permission('job-status-change');
                Route::post('reject-job/{id}', 'reject_job')->name('job.reject');
                Route::post('delete/{id}', 'delete_job')->name('job.delete')->permission('job-delete');
            });
        });

        // job history
        Route::controller(JobHistoryController::class)->group(function () {
            Route::group(['prefix' => 'job-history'], function () {
                Route::get('all', 'all_history')->name('job.history')->permission('job-history-list');
                Route::get('search-job', 'search_history')->name('job.history.search');
                Route::get('paginate/data', 'pagination')->name('job.history.paginate.data');
            });
        });

        // skill
        Route::controller(SkillController::class)->group(function () {
            Route::match(['get', 'post'], '/skills', 'all_skill')->name('skill')->permission('skill-list');
            Route::post('edit-skill/{id?}', 'edit_skill')->name('skill.edit')->permission('skill-edit');
            Route::post('change-status/{id}', 'change_status')->name('skill.status')->permission('skill-status-change');
            Route::post('delete/{id}', 'delete_skill')->name('skill.delete')->permission('skill-delete');
            Route::post('bulk-action', 'bulk_action_skill')->name('skill.delete.bulk.action')->permission('skill-bulk-delete');
            Route::get('paginate/data', 'pagination')->name('skill.paginate.data');
            Route::get('search-skill', 'search_skill')->name('skill.search');
        });

        // page settings
        Route::group(['prefix' => 'page-settings'], function () {

            Route::controller(LoginRegisterController::class)->group(function () {
                //user login page settings
                Route::match(['get', 'post'], 'login-page', 'login_page_settings')->name('page.settings.login')->permission('login-page-settings-view');
                //user register page settings
                Route::match(['get', 'post'], 'register-page', 'register_page_settings')->name('page.settings.register')->permission('register-page-settings-view');
                //register page recaptcha settings
                Route::match(['get', 'post'], 'register-page-recaptcha', 'register_page_recaptcha_settings')->name('page.settings.register.recaptcha');
            });

            //user account setup page settings
            Route::controller(AccountSettingsController::class)->group(function () {
                Route::match(['get', 'post'], 'account/main-page', 'main_page')->name('page.account.main.page')->permission('account-page-settings-view');
                Route::match(['get', 'post'], 'account/introduction', 'introduction')->name('page.account.introduction')->permission('introduction-page-settings-view');
                Route::match(['get', 'post'], 'account/experience', 'experience')->name('page.account.experience')->permission('experience-page-settings-view');
                Route::match(['get', 'post'], 'account/education', 'education')->name('page.account.education')->permission('education-page-settings-view');
                Route::match(['get', 'post'], 'account/work', 'work')->name('page.account.work')->permission('work-page-settings-view');
                Route::match(['get', 'post'], 'account/skill', 'skill')->name('page.account.skill')->permission('skill-page-settings-view');
                Route::match(['get', 'post'], 'account/rate-and-photo', 'rate_and_photo')->name('page.account.rate.photo')->permission('photo-page-settings-view');
            });

        });

        //admin manage
        Route::group(['prefix' => 'manage'], function () {
            Route::controller(AdminManageController::class)->group(function () {
                Route::get('all-admins', 'all_admins')->name('all');
                Route::match(['get', 'post'], 'create/new-admin', 'create_admin')->name('create');
                Route::match(['get', 'post'], 'edit/admin/{id}', 'edit_admin')->name('edit');
                Route::post('delete/admin/{id}', 'delete_admin')->name('delete');
                Route::post('change/admin/password', 'change_password')->name('password.change');
            });
        });

        //user manage
        Route::group(['prefix' => 'user'], function () {
            Route::controller(UserManageController::class)->group(function () {
                Route::match(['get', 'post'], 'add-user', 'add_user')->name('user.add');

                Route::get('all-clients', 'all_clients')->name('client.all')->permission('user-list');
                Route::get('paginate/data/client', 'client_pagination')->name('client.paginate.data');
                Route::get('search/client', 'search_client')->name('client.search');

                Route::get('all-freelancers', 'all_freelancers')->name('freelancer.all')->permission('user-list');
                Route::get('paginate/data/freelancer', 'freelancer_pagination')->name('freelancer.paginate.data');
                Route::get('search/freelancer', 'search_freelancer')->name('freelancer.search');

                Route::post('edit-user-info', 'edit_info')->name('user.info.edit')->permission('user-details-update');
                Route::post('change-user-password', 'change_password')->name('user.password.change')->permission('user-password-change');
                Route::post('identity-details', 'identity_details')->name('user.identity.details')->permission('user-identity-details');
                Route::post('identity-verify/status', 'identity_verify_status')->name('user.identity.verify.status')->permission('user-identity-status-update');
                Route::post('identity-verify/decline', 'identity_verify_decline')->name('user.identity.verify.decline')->permission('user-identity-decline');
                Route::post('change-user-active-inactive-status/{id}', 'change_status')->name('user.status');
                Route::post('delete/{id}', 'delete_user')->name('user.delete')->permission('user-delete');
                Route::post('permanent-delete/{user_id}', 'permanent_delete')->name('user.permanent.delete')->permission('user-delete');
                Route::match(['get', 'post'], 'user-restore/{id?}', 'user_restore')->name('user.restore')->permission('user-trash-list');
                Route::get('paginate/delete/data', 'pagination_delete_user')->name('user.paginate.delete.data');
                Route::get('delete/search-user', 'search_delete_user')->name('user.delete.search');

                Route::get('verification-request', 'verification_requests')->name('user.verification.request');
                Route::get('verification-request/paginate/data', 'verification_request_pagination')->name('user.identity.request.paginate.data');
                Route::get('verification-request/search-user', 'verification_request_search_user')->name('user.identity.request.search');
                Route::post('verification-request/notify-missing-details', 'notify_missing_details')->name('user.identity.notify.missing');

                Route::post('disable-2-factor-authentication/{id}', 'disable_2fa')->name('user.disable._2fa');
                Route::post('verify-user-email/{id}', 'verify_user_email')->name('user.verify.email');

                Route::post('admin/individual/commission/settings', 'individual_commission_settings')->name('user.individual.commission.settings')->permission('user-individual-commission-settings');
            });
        });

        //feedback freelancer
        Route::controller(FeedbackController::class)->group(function () {
            Route::get('feedback/all', 'all_feedback')->name('feedback.all');
            Route::post('feedback/edit', 'edit_feedback')->name('feedback.edit');
            Route::post('feedback/change-status/{id}', 'change_status')->name('feedback.status');
            Route::post('feedback/delete/{id}', 'delete_feedback')->name('feedback.delete');
            Route::get('feedback/paginate/data', 'pagination')->name('feedback.paginate.data');
            Route::get('feedback/search-feedback', 'search_feedback')->name('feedback.search');
        });


        //account suspend active
        Route::group(['prefix' => 'account'], function () {
            Route::controller(SuspendActiveController::class)->group(function () {
                Route::match(['get', 'post'], 'suspend/{id}', 'suspend')->name('account.suspend');
                Route::post('suspend/{id}', 'suspend')->name('account.suspend');
                Route::post('unsuspend/{id}', 'unsuspend')->name('account.unsuspend');
            });
        });

        //transaction manage
        Route::group(['prefix' => 'transaction'], function () {
            Route::controller(TransactionController::class)->group(function () {
                Route::match(['get', 'post'], 'commission/settings', 'commission_settings')->name('commission.settings')->permission('admin-commission-settings-view');
                Route::match(['get', 'post'], 'fee/settings', 'transaction_fee_settings')->name('transaction.fee.settings')->permission('transaction-fee-settings-view');
                Route::match(['get', 'post'], 'withdraw/fee/settings', 'withdraw_fee_settings')->name('withdraw.fee.settings')->permission('withdraw-fee-settings-view');
            });
        });

        // additional settings (needed by admin sidebar)
        Route::group(['prefix' => 'additional-settings'], function () {
            Route::controller(AdditionalSettingsController::class)->group(function () {
                Route::match(['get', 'post'], 'loader/settings', 'loader_settings')->name('loader.settings');
                Route::match(['get', 'post'], 'mouse/settings', 'mouse_settings')->name('mouse.settings');
                Route::match(['get', 'post'], 'bottom-to-top/settings', 'bottom_to_top_settings')->name('bottom.to.top.settings');
                Route::match(['get', 'post'], 'sticky-menu/settings', 'sticky_menu_settings')->name('sticky.menu.settings');
                Route::match(['get', 'post'], 'commission-display/settings', 'commission_display_settings')->name('commission.display.settings');
                Route::match(['get', 'post'], 'home-page-animation/settings', 'home_page_animation_settings')->name('home.animation.settings');
                Route::match(['get', 'post'], 'project-enable-disable/settings', 'project_enable_disable_settings')->name('project.enable.disable.settings');
                Route::match(['get', 'post'], 'job-enable-disable/settings', 'job_enable_disable_settings')->name('job.enable.disable.settings');
                Route::match(['get', 'post'], 'chat-email-enable-disable/settings', 'chat_enable_disable_settings')->name('chat.email.settings');
                Route::match(['get', 'post'], 'signup-bonus/settings', 'signup_bonus_settings')->name('signup.bonus.settings');
                Route::match(['get', 'post'], 'subscription-tax/settings', 'subscription_tax_settings')->name('subscription.tax.settings');
            });
        });

        // general settings -> affiliate settings (admin can set commission rates)
        Route::group(['prefix' => 'general-settings'], function () {
            Route::controller(\Modules\GeneralSettings\Http\Controllers\GeneralSettingsController::class)->group(function () {
                Route::match(['get', 'post'], 'affiliate-settings', 'affiliate_settings')->name('general.settings.affiliate')->permission('affiliate-settings');
            });
        });

        //order manage
        Route::group(['prefix' => 'order'], function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('all', 'all_orders')->name('order.all')->permission('order-list');
                Route::get('paginate/data', 'pagination')->name('order.paginate.data');
                Route::get('search-order', 'search_order')->name('order.search');
                Route::get('details/{id}', 'order_details')->name('order.details')->permission('order-details');
                Route::post('hold-order/{id}', 'hold_order')->name('order.hold')->permission('order-hold');
                Route::post('unhold-order/{id}', 'unhold_order')->name('order.unhold');
                Route::post('change-status/{id}', 'change_status')->name('order.status');
                Route::post('decline-order/{id}', 'decline_order')->name('order.decline');
                Route::post('update-manual-payment', 'update_manual_payment')->name('order.update.manual.payment')->permission('order-manual-payment-status-update');
                Route::get('order-sort-by-status', 'order_sort_by_status')->name('order.sort.by.status');
                Route::match(['get', 'post'], 'auto-approval-settings', 'auto_approval_settings')->name('order.approval.settings');
                Route::match(['get', 'post'], 'order-enable-disable-settings', 'order_enable_disable_settings')->name('order.enable.disable.settings');
                Route::match(['get', 'post'], 'order-enable-disable-milestone-settings', 'order_enable_disable_milestone_settings')->name('order.enable.disable.milestone.settings');
                Route::match(['get', 'post'], 'order-enable-disable-description-settings', 'order_enable_disable_description_settings')->name('order.enable.disable.description.settings');
            });
        });

        //order invoice
        Route::group(['prefix' => 'order/invoice'], function () {
            Route::controller(InvoiceController::class)->group(function () {
                Route::get('generate/{id}', 'generate_invoice')->name('order.invoice.generate');
            });
        });

        //user report manage
        Route::group(['prefix' => 'user-report'], function () {
            Route::controller(UserReportController::class)->group(function () {
                Route::get('all', 'all_reports')->name('user.report.all');
                Route::post('update', 'report_update')->name('user.report.update');
                Route::get('paginate/data', 'pagination')->name('user.report.paginate.data');
                Route::get('delete/{id}', 'delete_report')->name('user.report.delete');
            });
        });

        //newsletter manage
        Route::group(['prefix' => 'newsletter'], function () {
            Route::controller(\App\Http\Controllers\Backend\NewsletterController::class)->group(function () {
                Route::get('all', 'all_newsletter')->name('newsletter.email.all');
                Route::post('send-email', 'send_email')->name('newsletter.email.send');
                Route::match(['get', 'post'], 'send-email-to-all', 'send_email_to_all')->name('newsletter.email.send.to.all');
                Route::post('add-email', 'add_email')->name('newsletter.email.add');
                Route::get('verify-email-send/{token}', 'verify_email_send')->name('newsletter.verify.email.send');
                Route::post('delete-email/{id}', 'delete_email')->name('newsletter.email.delete');
                Route::get('paginate/data', 'pagination')->name('newsletter.email.paginate.data');
            });
        });

        // menu
        Route::group(['prefix' => 'plugins'], function () {
            //MENU BUILDER
            Route::group(['prefix' => 'menu'], function () {
                Route::controller(MenuController::class)->group(function () {
                    Route::match(['get', 'post'], '/all', 'menu')->name('menu')->permission('menu-list');
                    Route::get('/menu-edit/{id}', 'edit_menu')->name('menu.edit')->permission('menu-edit');
                    Route::post('/menu-update/{id}', 'update_menu')->name('menu.update')->permission('menu-edit');
                    Route::post('/menu-delete/{id}', 'delete_menu')->name('menu.delete')->permission('menu-delete');
                    Route::post('/menu-default/{id}', 'set_default_menu')->name('menu.default');
                    Route::post('/mega-menu', 'mega_menu_item_select_markup')->name('mega.menu.item.select.markup');
                });
            });

            // form builder
            Route::controller(FormBuilderController::class)->group(function () {
                Route::group(['prefix' => 'form'], function () {
                    Route::match(['get', 'post'], '/all', 'form')->name('form')->permission('form-list');
                    Route::get('/form-edit/{id}', 'edit_form')->name('form.edit')->permission('form-edit');
                    Route::post('/form-update/{id?}', 'update_form')->name('form.update')->permission('form-edit');
                    Route::post('/form-delete/{id}', 'delete_form')->name('form.delete')->permission('form-delete');
                    Route::post('/bulk-action', 'bulk_action')->name('delete.bulk.action.form')->permission('form-bulk-delete');
                });
            });

            // widget builder
            Route::controller(WidgetBuilderController::class)->group(function () {
                Route::group(['prefix' => 'widgets'], function () {
                    Route::get('/all', 'widgets')->name('widget')->permission('widget-list');
                    Route::post('/create', 'new_widget')->name('widgets.new')->permission('widget-add');
                    Route::post('/markup', 'widget_markup')->name('widgets.markup');
                    Route::post('/update', 'update_widget')->name('widgets.update')->permission('widget-update');
                    Route::post('/update/order', 'update_order_widget')->name('widgets.update.order')->permission('widget-update');
                    Route::post('/delete', 'delete_widget')->name('widgets.delete')->permission('widget-delete');
                });
            });

            //page builder
            Route::controller(PageBuilderController::class)->group(function () {
                Route::group(['prefix' => 'page-builder'], function () {

                    Route::post('update', 'update_addon_content')->name('page.builder.update');
                    Route::post('new', 'store_new_addon_content')->name('page.builder.new');
                    Route::post('delete', 'delete')->name('page.builder.delete');
                    Route::post('update-order', 'update_addon_order')->name('page.builder.update.addon.order');
                    Route::post('get-admin-markup', 'get_admin_panel_addon_markup')->name('page.builder.get.addon.markup');

                    /*-------------------------
                         CONTACT PAGE BUILDER
                    -------------------------*/
                    Route::get('contact-page', 'contactpage_builder')->name('contact.page.builder');
                    Route::post('contact-page', 'update_contactpage_builder');

                    /*-------------------------
                       DYNAMIC PAGE BUILDER
                    -------------------------*/
                    Route::get('dynamic-page/{type}/{id}', 'dynamicpage_builder')->name('dynamic.page.builder');
                    Route::post('dynamic-page', 'update_dynamicpage_builder')->name('dynamic.page.builder.store');
                });
            });

        });

        // media uploader
        Route::controller(MediaUploadController::class)->group(function () {
            Route::post('/media-upload/all', 'all_upload_media_file')->name('upload.media.file.all');
            Route::post('/media-upload', 'upload_media_file')->name('upload.media.file');
            Route::post('/media-upload/alt', 'alt_change_upload_media_file')->name('upload.media.file.alt.change');
            Route::post('/media-upload/delete', 'delete_upload_media_file')->name('upload.media.file.delete');
            Route::post('/media-upload/loadmore', 'get_image_for_loadmore')->name('upload.media.file.loadmore');
        });

        //language
        Route::controller(LanguageController::class)->group(function () {
            Route::get('languages/all', 'all_language')->name('languages')->permission('language-list');
            Route::post('languages/add', 'add_language')->name('languages.add')->permission('language-add');
            Route::post('languages/update', 'update_language')->name('languages.update')->permission('language-edit');
            Route::get('languages/words/all/{slug}', 'all_edit_words')->name('languages.words.all')->permission('language-word-list');
            Route::post('languages/words/update/{id}', 'update_words')->name('languages.words.update')->permission('language-word-edit');
            Route::post('languages/add-new-word', 'add_new_words')->name('languages.add.new.word')->permission('language-word-add');
            Route::post('languages/regenerate-source-text', 'regenerate_source_text')->name('languages.regenerate.source.texts');
            Route::get('languages/default/{id}', 'make_default')->name('languages.make.default');
        });

        Route::controller(ReferralInvitationController::class)->group(function () {
            Route::get('referral-invitations', 'index')->name('referral.invitations')->permission('referral-invitation-list');
            Route::get('referrals', 'referrals')->name('referrals')->permission('referral-list');

        });
    });



    Route::controller(AffiliateCommissionController::class)->group(function () {
        Route::get('/affiliate/commissions', 'index')->name('affiliate.commissions');
        Route::post('/affiliate/commissions/{id}/approve', 'approve')->name('affiliate.commission.approve');
        Route::post('/affiliate/commissions/{id}/reject', 'reject')->name('affiliate.commission.reject');
    });

    // 🔹 Affiliate Payouts
    Route::controller(AffiliatePayoutController::class)->group(function () {
        Route::get('/affiliate/payouts', 'index')->name('affiliate.payouts');
        Route::post('/affiliate/payouts/approve/{id}', 'approve')->name('affiliate.payouts.approve');
        Route::post('/affiliate/payouts/{id}/reject', 'reject')->name('affiliate.payouts.reject');
        Route::get('/affiliate/payouts/{id}', 'show')->name('affiliate.payouts.show');

    });

    Route::controller(AdminAffiliateSupportController::class)->group(function () {
        Route::get('/affiliate/support', 'index')->name('affiliate.support.index');         // list tickets
        Route::get('/affiliate/support/{id}', 'show')->name('affiliate.support.show');      // view ticket
        Route::post('/affiliate/support/{id}/reply', 'reply')->name('affiliate.support.reply'); // admin reply (ajax)
        Route::post('/affiliate/support/{id}/status', 'changeStatus')->name('affiliate.support.status'); // change status (ajax)
        Route::post('/affiliate/support/{id}/delete', 'destroy')->name('affiliate.support.delete'); // optional delete
    });

    Route::get('client-benefits', [ClientBenefitController::class, 'edit'])
        ->name('client.benefits');

    Route::post('client-benefits', [ClientBenefitController::class, 'update'])
        ->name('client.benefits.update');

    Route::get('talent-benefits', [TalentBenefitController::class, 'edit'])
        ->name('talent.benefits');

    Route::post('talent-benefits', [TalentBenefitController::class, 'update'])
        ->name('talent.benefits.update');

    Route::get('privacy-policy', [PrivacyPolicyController::class, 'edit'])
        ->name('privacy.policy.edit');
    Route::post('privacy-policy', [PrivacyPolicyController::class, 'update'])
        ->name('privacy.policy.update');

    Route::get('terms-of-service', [TermsOfServiceController::class, 'edit'])
        ->name('terms.of.service.edit');
    Route::post('terms-of-service', [TermsOfServiceController::class, 'update'])
        ->name('terms.of.service.update');

    Route::get('service-shipping-policy', [ServiceShippingPolicyController::class, 'edit'])
        ->name('service.shipping.policy.edit');
    Route::post('service-shipping-policy', [ServiceShippingPolicyController::class, 'update'])
        ->name('service.shipping.policy.update');

    Route::get('refund-return-policy', [RefundReturnPolicyController::class, 'edit'])
        ->name('refund.return.policy.edit');
    Route::post('refund-return-policy', [RefundReturnPolicyController::class, 'update'])
        ->name('refund.return.policy.update');

    Route::get('fees-and-charges', [\App\Http\Controllers\Backend\FeesAndChargeController::class, 'edit'])
        ->name('fees.charge.edit');
    Route::post('fees-and-charges', [\App\Http\Controllers\Backend\FeesAndChargeController::class, 'update'])
        ->name('fees.charge.update');

    // About Us Management
    Route::controller(AboutUsController::class)->group(function () {
        Route::get('about-us', 'index')->name('about-us.index');
        Route::get('about-us/create', 'create')->name('about-us.create');
        Route::post('about-us', 'store')->name('about-us.store');
        Route::get('about-us/{id}', 'show')->name('about-us.show');
        Route::get('about-us/{id}/edit', 'edit')->name('about-us.edit');
        Route::get('about-us/{id}/test', 'testEdit')->name('about-us.test');
        Route::put('about-us/{id}', 'update')->name('about-us.update');
        Route::delete('about-us/{id}', 'destroy')->name('about-us.destroy');
    });

    /*-----------------------------------
        PARTNERSHIP PAGE ROUTES
    ------------------------------------*/
    Route::prefix('partnership')->name('partnership.page.')->group(function () {
        Route::get('/edit', [\App\Http\Controllers\Backend\PartnershipPageController::class, 'edit'])->name('edit');
        Route::put('/update', [\App\Http\Controllers\Backend\PartnershipPageController::class, 'update'])->name('update');
    });

    Route::prefix('investor-relation')->name('investor.relation.page.')->group(function () {
        Route::get('/edit', [\App\Http\Controllers\Backend\InvestorRelationController::class, 'edit'])->name('edit');
        Route::put('/update', [\App\Http\Controllers\Backend\InvestorRelationController::class, 'update'])->name('update');
    });

    Route::prefix('trust-safety')->name('trust-safety.page.')->group(function () {
        Route::get('/edit', [TrustAndSafetyController::class, 'edit'])->name('edit');
        Route::put('/update', [TrustAndSafetyController::class, 'update'])->name('update');
    });

    Route::prefix('support')->name('support.page.')->group(function () {
        Route::get('/edit', [SupportController::class, 'edit'])->name('edit');
        Route::put('/update', [SupportController::class, 'update'])->name('update');
    });

    Route::prefix('win-work-with-rewards')->name('win-work-with-rewards.')->group(function () {
        Route::get('/edit', [WinWorkWithRewardsController::class, 'edit'])->name('edit');
        Route::post('/update', [WinWorkWithRewardsController::class, 'update'])->name('update');
    });

    Route::prefix('how-it-works')->name('how-it-works.')->group(function () {
        Route::get('/edit', [HowItWorksController::class, 'edit'])->name('edit');
        Route::put('/update', [HowItWorksController::class, 'update'])->name('update');
    });

    Route::prefix('affiliate-programme')->name('affiliate-programme.')->group(function () {
        Route::get('/edit', [AffiliateProgrammeController::class, 'edit'])->name('edit');
        Route::post('/update', [AffiliateProgrammeController::class, 'update'])->name('update');
    });

    // SEO Settings
    Route::controller(SeoSettingController::class)->prefix('seo')->name('seo.')->group(function () {
        Route::get('/', 'index')->name('index'); // we might need permission here if Role package is fully used
        Route::post('/store', 'store')->name('store');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
    });

});
