<?php

use App\Http\Controllers\Auth\DI\AuthDI;
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


Route::prefix('/auth')->group(function () {

    Route::get('/check-auth', [AuthDI::class, 'check_auth']);

    Route::post('/register', [AuthDI::class, 'register']);

    Route::post('/login', [AuthDI::class, 'login']);

    Route::post('/google-auth', [AuthDI::class, 'google_auth']);
});
