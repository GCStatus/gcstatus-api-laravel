<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    Steam\SteamController,
};

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api", "api.auth", "admin.scopes" middleware group.
| Make something great!
|
*/

Route::post('steam/apps/create', SteamController::class)->middleware(
    'scopes:view:games,create:games,create:steam-apps',
)->name('steam.apps.create');
