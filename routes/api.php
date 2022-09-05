<?php

use Illuminate\Http\Request;
use App\Http\Controllers\LocationController;

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
    Route::post('/', 'BandungSubmissionController@create');

    Route::group(['middleware' => 'auth'], function () {
        Route::post('/event_checkin', 'BandungSubmissionController@eventCheckin');
    });
});
//

Route::group(['prefix' => 'admin'], function () {
    Route::post('login', 'AuthController@login')->name('login');
});

Route::group(['prefix' => 'location'], function () {
    Route::get('province', [LocationController::class, 'province']);
    Route::get('city', [LocationController::class, 'city']);
    Route::get('district', [LocationController::class, 'district']);
    Route::get('sub_district', [LocationController::class, 'subDistrict']);
});
