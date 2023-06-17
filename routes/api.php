<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * "/index" is the url
 * 'index' is the method defined in TestController
 * http://127.0.0.1:8000/api/index
 */
//Route::get("/index", [TestController::class, 'index']);
//or
Route::get("/index", 'App\Http\Controllers\TestController@index');

//Route::post("/login", [LoginController::class, 'login']);
//Route::get("/login2", [LoginController::class, 'login2']);


/**
 * "namespace" is telling laravel where to find a certain controller. i.e. app/Http/Controller/Api
 * @login, @get_profile are the method defined in TestController
 * http://127.0.0.1:8000/api/login
 */
Route::group(['namespace'=>'App\Http\Controllers\Api'], function(){
    Route::any('/login', 'LoginController@login');
    Route::any('/get_profile', 'LoginController@get_profile');
    Route::any("/login2", 'LoginController@login2');
    Route::any("/contact", 'LoginController@contact')->middleware('CheckUser');

    Route::any('/get_rtc_token', 'AccessTokenController@get_rtc_token')->middleware('CheckUser');

    Route::any('/send_notice', 'LoginController@send_notice')->middleware('CheckUser');
    Route::any('/bind_fcmtoken', 'LoginController@bind_fcmtoken')->middleware('CheckUser');

});

//
