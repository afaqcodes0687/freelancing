<?php

use Illuminate\Http\Request;

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

Route::post('subscriptions/paypro/webhook', [\Modules\Subscription\Http\Controllers\Frontend\PayProController::class, 'webhook'])->name('subscriptions.paypro.webhook');

Route::middleware('auth:api')->get('/subscription', function (Request $request) {
    return $request->user();
});