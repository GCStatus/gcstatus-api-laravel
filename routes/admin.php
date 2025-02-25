<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    MeController,
    DlcController,
    TagController,
    GameController,
    GenreController,
    StoreController,
    CriticController,
    CrackerController,
    CategoryController,
    PlatformController,
    LanguageController,
    DeveloperController,
    MediaTypeController,
    PublisherController,
    ProtectionController,
    Steam\SteamController,
    RequirementTypeController,
    TorrentProviderController,
    TransactionTypeController,
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

Route::apiResource('dlcs', DlcController::class);
Route::apiResource('tags', TagController::class)->except('show');
Route::apiResource('genres', GenreController::class)->except('show');
Route::apiResource('stores', StoreController::class)->except('show');
Route::apiResource('games', GameController::class)->names('admin.games');
Route::apiResource('critics', CriticController::class)->except('show');
Route::apiResource('crackers', CrackerController::class)->except('show');
Route::apiResource('platforms', PlatformController::class)->except('show');
Route::apiResource('languages', LanguageController::class)->except('show');
Route::apiResource('categories', CategoryController::class)->except('show');
Route::apiResource('publishers', PublisherController::class)->except('show');
Route::apiResource('developers', DeveloperController::class)->except('show');
Route::apiResource('media-types', MediaTypeController::class)->except('show');
Route::apiResource('protections', ProtectionController::class)->except('show');
Route::apiResource('transaction-types', TransactionTypeController::class)->except('show');
Route::apiResource('requirement-types', RequirementTypeController::class)->except('show');
Route::apiResource('torrent-providers', TorrentProviderController::class)->except('show');
