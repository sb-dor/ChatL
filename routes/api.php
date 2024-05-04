<?php

use App\Http\Controllers\Auth\DI\AuthDI;
use App\Http\Controllers\AuthController;
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


Route::prefix('/auth')->group(function () {

    Route::get('/check-auth', [AuthDI::class, 'check_auth']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/register', [AuthDI::class, 'register']);

        Route::post('/login', [AuthDI::class, 'login']);
    });
});
