<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
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
// Revamp
//

Route::group(['as' => 'customer.'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Route::group(['prefix' => 'vouchers', 'as' => 'vouchers.'], function () {
        //     Route::get('/', [VoucherController::class, 'index'])->name('index');
        // });

        // Route::group(['prefix' => 'articles', 'as' => 'articles.'], function () {
        //     Route::get('/', [ArticleController::class, 'index'])->name('index');
        //     Route::get('{voucher}/detail', [ArticleController::class, 'detail'])->name('detail');
        // });

        // Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
        //     Route::get('/', [OrderController::class, 'index'])->name('index');
        //     Route::get('{voucher}/detail', [OrderController::class, 'detail'])->name('detail');
        // });

        // Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function () {
        //     Route::get('/', [ContactController::class, 'index'])->name('index');
        //     Route::get('{contact}/detail', [ContactController::class, 'detail'])->name('detail');
        // });

        // Route::group(['prefix' => 'faqs', 'as' => 'faqs.'], function () {
        //     Route::get('{faq}/detail', [FaqController::class, 'detail'])->name('detail');
        // });
    });
});
