<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ArticleController;
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

Route::get('/', function () {
    return 'Pristine Official API';
});

// Bandung Microsite Submission
Route::group(['prefix' => 'bandung_submission'], function () {
    Route::get('/generate_token', 'BandungSubmissionController@generateToken');
    Route::get('/get_total', 'BandungSubmissionController@getTotal');
    Route::get('/download_result_image', 'BandungSubmissionController@downloadResultImage');
    Route::get('/decrypt_table', 'BandungSubmissionController@decryptTable');
    Route::get('/send_bulk_invitation_mail', 'BandungSubmissionController@sendBulkInvitationMail');
    Route::get('/send_invitation_mail', 'BandungSubmissionController@sendInvitationMail');
    Route::post('/', 'BandungSubmissionController@create');

    Route::group(['middleware' => 'auth'], function () {
        Route::post('/event_checkin', 'BandungSubmissionController@eventCheckin');
    });
});
//

Route::group(['prefix' => 'admin'], function () {
    Route::post('login', 'AuthController@login')->name('login');

    // User resources
    Route::resource('users', 'UserController');

    // Pristime photo resources
    Route::resource('pristime_photos', 'PristimePhotoController');
});

// Pristime Photo
Route::get('/pristime_photos', 'PristimePhotoController@index');
Route::get('/pristime_photos/{id}', 'PristimePhotoController@show');
Route::post('/pristime_photos/download', 'PristimePhotoController@downloadPhoto');
//

Route::group(['prefix' => 'location'], function () {
    Route::get('province', [LocationController::class, 'province']);
    Route::get('city', [LocationController::class, 'city']);
    Route::get('district', [LocationController::class, 'district']);
    Route::get('sub_district', [LocationController::class, 'subDistrict']);
});

Route::group(['prefix' => 'promo', 'as' => 'promo.'], function () {
    Route::get('/', [PromoController::class, 'index'])->name('list');
    Route::post('create', [PromoController::class, 'create'])->name('create');
    Route::post('{promo}/update', [PromoController::class, 'update'])->name('update');
    Route::delete('{promo}/delete', [PromoController::class, 'delete'])->name('delete');
});

Route::group(['prefix' => 'article', 'as' => 'article.'], function () {
    Route::get('/', [ArticleController::class, 'index'])->name('list');
    Route::post('create', [ArticleController::class, 'create'])->name('create');
    Route::post('{article}/update', [ArticleController::class, 'update'])->name('update');
    Route::delete('{article}/delete', [ArticleController::class, 'delete'])->name('delete');
});
