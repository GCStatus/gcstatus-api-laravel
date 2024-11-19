<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
};

/*
|--------------------------------------------------------------------------
| API Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" and "auth" middleware group. Make something great!
|
*/

Route::controller(UserController::class)->group(function () {
    Route::get('me', 'me')->name('auth.me');
});
