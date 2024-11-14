<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\LoginController,
    Auth\SocialiteController,
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', LoginController::class)->name('auth.login');
Route::controller(SocialiteController::class)->prefix('oauth/{provider}')->group(function () {
    Route::get('redirect', 'redirect')->name('auth.socialite.redirect');
    Route::get('callback', 'callback')->name('auth.socialite.callback');
});
