<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\PlaylistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/playlists', [\App\Services\SpotifyService::class, 'createPlaylist']);
    Route::get('/playlists', [PlaylistController::class, 'index']);
    Route::get('/playlists/{id}', [PlaylistController::class, 'show']);
    Route::post('/playlists/{id}/add-tracks', [\App\Services\SpotifyService::class, 'addPlaylistTracks']);
});

Route::prefix('/auth')->group(function(){
    Route::post('/register', [AuthController::class, 'registerSocialUser']);
    Route::post('/login', [AuthController::class, 'loginUser']);
    Route::get('/{id}', [AuthController::class, 'getUser']);
    Route::put('me/update', [AuthController::class, 'updateUser']);
});
