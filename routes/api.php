<?php

use App\Http\Controllers\Auth\DI\AuthDI;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\WebRTCController;
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

    Route::post('/register', [AuthDI::class, 'register']);

    Route::post('/login', [AuthDI::class, 'login']);

    Route::post('/google-auth', [AuthDI::class, 'google_auth']);

    Route::post('/facebook-auth', [AuthDI::class, 'facebook_auth']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/check-auth', [AuthDI::class, 'check_auth']);
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

    // VIDEO CHAT STREAM ROUTES
    Route::prefix('/chats/video-stream')->group(function () {

        Route::put('/videochat/entrance', [VideoStreamController::class, 'videochat_entrance']);

        //
        Route::put('/start/videochat', [VideoStreamController::class, 'start_video_chat']);

        Route::put("/leave/videochat", [VideoStreamController::class, 'leave_video_chat']);

        Route::put('/video/stream', [VideoStreamController::class, 'video_stream']);
    });
});

// web-rtc routes
Route::post('/create-room', [WebRTCController::class, 'createRoom']);
Route::post('/join-room', [WebRTCController::class, 'joinRoom']);
Route::post('/add-ice-candidate', [WebRTCController::class, 'addIceCandidate']);
Route::get('/get-ice-candidates/{roomId}/{role}', [WebRTCController::class, 'getIceCandidates']);
Route::post('/candidat-data-handler', [WebRTCController::class, 'candidat_data_handler']);

// test_for
Route::get('/test_message', [ChatController::class, 'test_message']);
Route::post('/post/images/test', [ChatController::class, 'post_images']);