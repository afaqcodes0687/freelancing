<?php

use Illuminate\Support\Facades\Route;
use Modules\Meeting\App\Http\Controllers\MeetingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth', 'userEmailVerify', 'globalVariable']], function () {
    // Client Routes
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
        Route::get('meeting', [MeetingController::class, 'index'])->name('meeting.index');
        Route::get('meeting/google/redirect', [MeetingController::class, 'redirectToGoogle'])->name('meeting.google.redirect');
    });

    // Freelancer Routes
    Route::group(['prefix' => 'freelancer', 'as' => 'freelancer.'], function () {
        Route::get('meeting', [MeetingController::class, 'index'])->name('meeting.index');
        Route::get('meeting/google/redirect', [MeetingController::class, 'redirectToGoogle'])->name('meeting.google.redirect');
    });

    Route::get('google/callback', [MeetingController::class, 'handleGoogleCallback'])->name('meeting.google.callback');
    Route::post('meeting/schedule', [MeetingController::class, 'schedule'])->name('meeting.schedule');
});

Route::group(['prefix' => 'admin/meeting', 'as' => 'admin.', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::get('google-settings', [\Modules\Meeting\App\Http\Controllers\AdminMeetingController::class, 'googleSettings'])->name('meeting.google.settings');
    Route::get('all', [\Modules\Meeting\App\Http\Controllers\AdminMeetingController::class, 'allMeetings'])->name('meeting.all');
    Route::get('search', [\Modules\Meeting\App\Http\Controllers\AdminMeetingController::class, 'search_meeting'])->name('meeting.search');
    Route::get('paginate', [\Modules\Meeting\App\Http\Controllers\AdminMeetingController::class, 'paginate'])->name('meeting.paginate');
    Route::post('settings-update', [\Modules\Meeting\App\Http\Controllers\AdminMeetingController::class, 'updateSettings'])->name('meeting.settings.update');
    Route::get('google/redirect', [\Modules\Meeting\App\Http\Controllers\AdminMeetingController::class, 'redirectToGoogle'])->name('meeting.google.redirect');
    Route::get('google/callback', [\Modules\Meeting\App\Http\Controllers\AdminMeetingController::class, 'handleGoogleCallback'])->name('meeting.google.callback');
});
