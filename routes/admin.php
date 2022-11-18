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

Route::group(['as' => 'admin.'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');


    Route::group(['middleware' => 'auth'], function () {
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::get('logout', [AuthController::class], 'logout')->name('logout');

        Route::group(['prefix' => 'vouchers', 'as' => 'vouchers.'], function () {
            Route::get('/', [VoucherController::class, 'index'])->name('index');
            Route::post('create', [VoucherController::class, 'create'])->name('create');
            Route::get('{voucher}/detal', [VoucherController::class, 'detail'])->name('detail');
            Route::post('{voucher}/update', [VoucherController::class, 'update'])->name('update');
            Route::delete('{voucher}/delete', [VoucherController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'articles', 'as' => 'articles.'], function () {
            Route::get('/', [ArticleController::class, 'index'])->name('index');
            Route::post('create', [ArticleController::class, 'create'])->name('create');
            Route::get('{voucher}/detal', [ArticleController::class, 'detail'])->name('detail');
            Route::post('{voucher}/update', [ArticleController::class, 'update'])->name('update');
            Route::delete('{voucher}/delete', [ArticleController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::post('create', [OrderController::class, 'create'])->name('create');
            Route::get('{voucher}/detal', [OrderController::class, 'detail'])->name('detail');
            Route::post('{voucher}/update', [OrderController::class, 'update'])->name('update');
            Route::delete('{voucher}/delete', [OrderController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function () {
            Route::get('/', [ContactController::class, 'index'])->name('index');
            Route::post('create', [ContactController::class, 'create'])->name('create');
            Route::get('{voucher}/detal', [ContactController::class, 'detail'])->name('detail');
            Route::post('{voucher}/update', [ContactController::class, 'update'])->name('update');
            Route::delete('{voucher}/delete', [ContactController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'faqs', 'as' => 'faqs.'], function () {
            Route::get('/', [FaqController::class, 'index'])->name('index');
            Route::post('create', [FaqController::class, 'create'])->name('create');
            Route::get('{faq}/detal', [FaqController::class, 'detail'])->name('detail');
            Route::post('{faq}/update', [FaqController::class, 'update'])->name('update');
            Route::delete('{faq}/delete', [FaqController::class, 'delete'])->name('delete');
        });
    });
});
