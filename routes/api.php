<?php

use App\Http\Controllers\Auth\DI\AuthDI;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactsController;
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


// AUTH ROUTES
Route::prefix('/auth')->group(function () {

    Route::get('/check-auth', [AuthDI::class, 'check_auth']);

    Route::post('/register', [AuthDI::class, 'register']);

    Route::post('/login', [AuthDI::class, 'login']);

    Route::post('/google-auth', [AuthDI::class, 'google_auth']);

    Route::post('/facebook-auth', [AuthDI::class, 'facebook_auth']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', [AuthDI::class, 'logout']);
    });
});


Route::middleware('auth:sanctum')->group(function () {

    // CONTACTS ROUTES
    Route::prefix('/contacts')->group(function () {

        Route::get('/search', [ContactsController::class, 'search_contacts']);

        Route::put('/add-contact', [ContactsController::class, 'add_contact']);
    });


    // CHATS ROUTES
    Route::prefix('/chats')->group(function () {

        Route::get('/get/chats', [ChatController::class, 'get_all_users_chat']);

        Route::any('/get/chat/on/entrance', [ChatController::class, 'get_user_chat_on_entrance']);

        Route::delete('/delete/temp/created/chats', [ChatController::class, 'delete_temp_created_chats']);

        Route::post('/message/handler', [ChatController::class, 'message_handler']);
    });
});


// test_for
Route::get('/test_message', [ChatController::class, 'test_message']);
