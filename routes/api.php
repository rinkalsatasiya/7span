<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;

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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    
    Route::group(['middleware' => ['admin_access']], function () {
        Route::resource('users', UserController::class);
    });
    Route::group(['middleware' => ['user_access']], function () {
        Route::post('users_profile_update', [UserController::class, 'profileUpdate']);
    });
    
});


