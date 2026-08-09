<?php

use Illuminate\Http\Request;
use Modules\Chat\Http\Controllers\Api\Freelancer\ReportController as FreelancerReportController;
use Modules\Chat\Http\Controllers\Api\Client\ReportController as ClientReportController;
use Modules\Chat\Http\Controllers\Api\Freelancer\ChatFavoriteController as FreelancerChatFavoriteController;
use Modules\Chat\Http\Controllers\Api\Client\ChatFavoriteController as ClientChatFavoriteController;
use Modules\Chat\Http\Controllers\Api\Client\ChatArchiveController as ClientChatArchiveController;
use Modules\Chat\Http\Controllers\Api\Freelancer\ChatArchiveController as FreelancerChatArchiveController;

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

Route::middleware('auth:api')->get('/$LOWER_NAME$', function (Request $request) {
    return $request->user();
});

// Freelancer Report Routes
Route::group(['prefix' => 'freelancer', 'middleware' => ['auth:sanctum']], function () {
    Route::controller(FreelancerReportController::class)->group(function () {
        Route::post('/report', 'store')->name('api.freelancer.report.store');
        Route::get('/reports', 'index')->name('api.freelancer.report.index');
        Route::get('/report/{id}', 'show')->name('api.freelancer.report.show');
    });

    // Freelancer Chat Favorite Routes
    Route::controller(FreelancerChatFavoriteController::class)->group(function () {
        Route::post('/chat-favorite/toggle', 'toggle')->name('api.freelancer.chat.favorite.toggle');
        Route::get('/chat-favorites', 'index')->name('api.freelancer.chat.favorites');
        Route::get('/chat-favorite/check', 'check')->name('api.freelancer.chat.favorite.check');
    });

    // Freelancer Chat Archive Routes
    Route::controller(FreelancerChatArchiveController::class)->group(function () {
        Route::post('/chat-archive', 'archive')->name('api.freelancer.chat.archive');
        Route::post('/chat-restore', 'restore')->name('api.freelancer.chat.restore');
        Route::post('/end-conversation', 'end_conversation')->name('api.freelancer.chat.end');
        Route::get('/chat-archives', 'index')->name('api.freelancer.chat.archives');
    });
});

// Client Report Routes
Route::group(['prefix' => 'client', 'middleware' => ['auth:sanctum']], function () {
    Route::controller(ClientReportController::class)->group(function () {
        Route::post('/report', 'store')->name('api.client.report.store');
        Route::get('/reports', 'index')->name('api.client.report.index');
        Route::get('/report/{id}', 'show')->name('api.client.report.show');
    });

    // Client Chat Favorite Routes
    Route::controller(ClientChatFavoriteController::class)->group(function () {
        Route::post('/chat-favorite/toggle', 'toggle')->name('api.client.chat.favorite.toggle');
        Route::get('/chat-favorites', 'index')->name('api.client.chat.favorites');
        Route::get('/chat-favorite/check', 'check')->name('api.client.chat.favorite.check');
    });

    // Client Chat Archive Routes
    Route::controller(ClientChatArchiveController::class)->group(function () {
        Route::post('/chat-archive', 'archive')->name('api.client.chat.archive');
        Route::post('/chat-restore', 'restore')->name('api.client.chat.restore');
        Route::post('/end-conversation', 'end_conversation')->name('api.client.chat.end');
        Route::get('/chat-archives', 'index')->name('api.client.chat.archives');
    });
});