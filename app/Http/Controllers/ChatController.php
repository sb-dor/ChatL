<?php

namespace App\Http\Controllers;

use App\Events\ChatNotifyEvent;
use App\Models\Chat;
use App\Models\User;
use App\Traits\ResponsesTrait;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    use ResponsesTrait;

    public function get_all_users_chat(Request $request)
    {

        $user = $request->user();

        $chats = User::join('chat_participants', 'chats.id', 'chat_participants.chat_id')
            ->where('chat_participants.id', $user->id)
            ->select('chats.*')
            ->with('chat_last_message')
            ->get();

        return $this->success(['chats' => $chats]);
    }

    public function test_message()
    {
        $chat = new Chat();
        $channel_name = "chat_notify_of_user_2";

        event(new ChatNotifyEvent($channel_name, $chat));
    }
}
