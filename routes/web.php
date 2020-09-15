<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login/{provider}', [AuthController::class, 'redirectToProvider'])
    ->where(['provider' => 'apple|spotify']);
Route::get('login/{provider}/callback', [AuthController::class, 'handleProviderCallback'])
    ->where(['provider' => 'apple|spotify']);



