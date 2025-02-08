<?php

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

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Customer\PassangerController;
use App\Http\Controllers\Customer\TicketOrderController;
use App\Http\Controllers\Customer\TravelScheduleController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('customer/passangers', PassangerController::class);
    Route::resource('customer/travel-schedules', TravelScheduleController::class);
    Route::resource('customer/ticket-orders', TicketOrderController::class);
});