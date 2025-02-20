<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    MeController,
    TagController,
    GenreController,
    CategoryController,
    PlatformController,
    DeveloperController,
    PublisherController,
    Steam\SteamController,
    StoreController,
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

Route::get('me', MeController::class)->name('admin.me');
Route::post('steam/apps/create', SteamController::class)->middleware(
    'scopes:view:games,create:games,create:steam-apps',
)->name('steam.apps.create');

Route::apiResource('tags', TagController::class)->except('show');
Route::apiResource('genres', GenreController::class)->except('show');
Route::apiResource('stores', StoreController::class)->except('show');
Route::apiResource('platforms', PlatformController::class)->except('show');
Route::apiResource('categories', CategoryController::class)->except('show');
Route::apiResource('publishers', PublisherController::class)->except('show');
Route::apiResource('developers', DeveloperController::class)->except('show');
