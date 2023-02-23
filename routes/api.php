<?php

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
