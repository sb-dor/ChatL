<?php

namespace App\Http\Controllers;

use App\Events\ChatNotifyEvent;
use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Traits\ResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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


    public function get_user_chat_on_entrance(Request $request)
    {
        $current_user = $request->user();

        $user_ids = [$current_user->id, $request->get('with_user_id')];

        $findChat = Chat::where('chat_id', $request->get('chat_id'))->first();

        if (!$findChat) {

            $time = date('Y-m-d H:i:s');

            $findChat = Chat::create([
                "chat_uuid" => Str::uuid(),
                'name' => '_temp_name_',
                'description' => "_temp_description_",
                'temporary_chat' => true,
                'created_at' => $time,
            ]);

            foreach ($user_ids as $user_id) {
                ChatParticipant::create([
                    'chat_id' => $findChat->id,
                    'user_id' => $user_id,
                    'participate_at' => $time,
                ]);
            }
        }

        $findChat = $findChat->load('participants');

        return $this->success(['chat' => $findChat]);
    }

    public function test_message()
    {
        $chat = new Chat();
        $channel_name = "chat_notify_of_user_2";

        event(new ChatNotifyEvent($channel_name, $chat));
    }
}
