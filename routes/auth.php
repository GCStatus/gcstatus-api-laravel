<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    LevelController,
    TitleController,
    Auth\LogoutController,
    TransactionController,
    NotificationController,
    Profile\SocialController,
    Profile\PictureController,
    EmailVerify\NotifyController,
    EmailVerify\VerifyController,
    Profile\ResetPasswordController,
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
Route::get('email/verify/{id}/{hash}', VerifyController::class)->middleware('signed')->name('verification.verify');

Route::middleware(['registration.should.complete'])->group(function () {
    Route::put('profiles/socials/update', SocialController::class)->name('profiles.socials.update');
    Route::put('profiles/picture/update', PictureController::class)->name('profiles.picture.update');
    Route::put('profiles/password/update', ResetPasswordController::class)->name('profiles.password.update');
    Route::get('levels', LevelController::class)->name('levels.index');
    Route::get('titles', TitleController::class)->name('titles.index');
    Route::apiResource('transactions', TransactionController::class)->only('index', 'destroy');
    Route::apiResource('notifications', NotificationController::class)->only('index', 'destroy');

    Route::controller(NotificationController::class)->group(function () {
        Route::put('notifications/all/read', 'markAllAsRead')->name('notifications.mark-all-as-read');
        Route::delete('notifications/all/remove', 'removeAll')->name('notifications.remove-all');
        Route::put('notifications/{id}/read', 'markAsRead')->name('notifications.mark-as-read');
        Route::put('notifications/{id}/unread', 'markAsUnread')->name('notifications.mark-as-unread');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('me', 'me')->name('auth.me');
        Route::put('users/basics/update', 'updateBasics')->name('users.basics.update');
        Route::put('users/sensitives/update', 'updateSensitives')->name('users.sensitives.update');
    });
});
