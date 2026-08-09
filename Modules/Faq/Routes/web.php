<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Route;
use Modules\Faq\Http\Controllers\Backend\FaqController;
use Modules\Faq\Http\Controllers\Frontend\QuestionController;

Route::controller(QuestionController::class)->group(function () {
    Route::post('/faq/question/by/user', 'question')->name('faq.question');
});


//admin routes

Route::group(['as'=>'admin.','prefix'=>'admin'],function(){
    Route::group(['middleware' => ['auth:admin','setlang']],function() {
        Route::controller(FaqController::class)->group(function () {
            Route::match(['get','post'], 'faq/all', 'faq_all')->name('faq.all');
            Route::post('faq/edit', 'edit_faq')->name('faq.edit');
            Route::get('faq/delete/{id}', 'delete_faq')->name('faq.delete');
            Route::get('faq/search-faq', 'search_faq')->name('faq.search');
            Route::get('faq/paginate/data', 'pagination')->name('faq.paginate.data');
        });
    });
});
