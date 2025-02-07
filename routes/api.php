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

use App\Http\Controllers\Api\Master\ProvinsiController;
use Illuminate\Support\Facades\Route;

Route::resource('master/provinsi', ProvinsiController::class);
Route::resource('master/kota', ProvinsiController::class);