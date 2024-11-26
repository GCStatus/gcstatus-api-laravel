<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    LevelController,
    Auth\LogoutController,
    Profile\SocialController,
    EmailVerify\NotifyController,
    EmailVerify\VerifyController,
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

Route::post('logout', LogoutController::class)->name('auth.logout');
Route::get('email/verify/notify', NotifyController::class)->name('verification.send');
Route::put('register/complete', CompleteRegistrationController::class)->name('auth.register.complete');
Route::get('/email/verify/{id}/{hash}', VerifyController::class)->middleware('signed')->name('verification.verify');

Route::middleware(['registration.should.complete'])->group(function () {
    Route::put('profiles/socials/update', SocialController::class)->name('profiles.socials.update');
    Route::apiResource('levels', LevelController::class)->only('index');
    Route::controller(UserController::class)->group(function () {
        Route::get('me', 'me')->name('auth.me');
    });
});
