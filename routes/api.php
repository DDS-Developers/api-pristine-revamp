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

// Bandung Microsite Submission
Route::post('/bandung_submission', 'BandungSubmissionController@create');

//

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => 'location'], function () {
    Route::get('province', [LocationController::class, 'province']);
    Route::get('city', [LocationController::class, 'city']);
    Route::get('district', [LocationController::class, 'district']);
    Route::get('sub_district', [LocationController::class, 'subDistrict']);
});
