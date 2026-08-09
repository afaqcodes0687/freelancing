<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliateDashboardController;
use App\Http\Controllers\AffiliateProfileController;
use App\Http\Controllers\AffiliateToolController;
use App\Http\Controllers\AffiliateClickController;
use App\Http\Controllers\AffiliateCommissionController;
use App\Http\Controllers\AffiliatePayoutController;
use App\Http\Controllers\AffiliateSupportController;

/*
|---------------------------------------------------------------------------
| Affiliate protected routes
|---------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'affiliate',
    'as' => 'affiliate.',
    'middleware' => ['globalVariable', 'maintains_mode', 'setlang', 'check.subscription', 'affiliateAuth']
], function () {

    // Dashboard
    Route::get('dashboard', [AffiliateDashboardController::class, 'dashboard'])->name('dashboard');

    // Profile (you already had this)
    Route::controller(AffiliateProfileController::class)->group(function () {
        Route::get('profile/settings', 'profile')->name('profile.settings');
        Route::post('profile/edit-profile', 'edit_profile')->name('profile.edit');
        Route::post('profile/edit-profile-photo', 'edit_profile_photo')->name('profile.photo.edit');
    });

    // Tools / Referral link + creatives
    Route::get('tools', [AffiliateToolController::class, 'index'])->name('tools');

    // Clicks report
    Route::get('clicks', [AffiliateClickController::class, 'index'])->name('clicks');

    // Commissions (list, detail)
    Route::get('commissions', [AffiliateCommissionController::class, 'index'])->name('commissions');
    Route::get('commissions/{id}', [AffiliateCommissionController::class, 'show'])->name('commissions.show');

    // Payouts
    Route::get('payouts', [AffiliatePayoutController::class, 'index'])->name('payouts');
    Route::post('payouts/request', [AffiliatePayoutController::class, 'requestPayout'])->name('payouts.request');

    // Support
    Route::get('support', [AffiliateSupportController::class, 'index'])->name('support');
    Route::post('support/send', [AffiliateSupportController::class, 'send'])->name('support.send');

    // Logout
    Route::get('logout', function () {
        session()->forget('logged_in_affiliate_id');
        return redirect()->route('affiliate.login')->with('success', 'Logged out successfully.');
    })->name('logout');

});
