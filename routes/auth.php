<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    Auth\CompleteRegistrationController,
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

Route::put('register/complete', CompleteRegistrationController::class)->name('auth.register.complete');

Route::middleware(['registration.should.complete'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('me', 'me')->name('auth.me');
    });
});
