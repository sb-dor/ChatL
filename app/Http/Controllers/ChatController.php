<?php

namespace App\Http\Controllers;

use App\Events\ChatNotifyEvent;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Traits\ResponsesTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{

    use ResponsesTrait;

    public function get_all_users_chat(Request $request)
    {

        $user = $request->user();

        $chats = User
            ::join('chat_participants', 'chats.id', 'chat_participants.chat_id')
            ->where('chat_participants.id', $user->id)
            ->whereNull('temporary_chat')
            ->select('chats.*')
            ->with('chat_last_message')
            ->get();

        return $this->success(['chats' => $chats]);
    }


    public function get_user_chat_on_entrance(Request $request)
    {
        try {
            $current_user = $request->user();

            $user_ids = [$current_user->id, $request->get('with_user_id')];

            $findChat = Chat::where('chat_uuid', $request->get('chat_uuid'))->first();

            if (!$findChat) {

                $this->find_and_delete_users_temporary_created_chat($user_ids);

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
        } catch (Exception $e) {
            return $this->fail(['message' => $e->getMessage()]);
        }
    }


    public function message_handler(Request $request)
    {

        $message = ChatMessage::create([
            "chat_id" => $request->get("chat_id"),
            "user_id" => $request->get("user_id"),
            "related_to_user_id" => $request->get("related_to_user_id"),
            "chat_message_uuid" => $request->get('"chat_message_uuid'),
            "message" => $request->get("message"),
            "created_at" => $request->get("created_at"),
        ]);

    }

    public function delete_temp_created_chats(Request $request)
    {
        Chat::where('id', $request->get('chat_id'))
            ->where('chat_uuid', $request->get('chat_uuid'))
            ->whereNotNull('temporary_chat')
            ->delete();

        return $this->success(['chat_id' => $request->get('chat_id'), "chat_uuid" => $request->get('chat_uuid')]);
    }

    // it's necessary, when user turned off the app without going back and the chat which was created temporary wasn't deleted
    private function find_and_delete_users_temporary_created_chat($user_ids = [])
    {

        $usersChatsIds = Chat::leftJoin('chat_participants', 'chats.id', 'chat_participants.chat_id')
            ->where(function ($sql) use ($user_ids) {
                for ($i = 0; $i < count($user_ids); $i++) {
                    if ($i == 0) {
                        $sql->where('chat_participants.user_id', $user_ids[$i]);
                    } else {
                        $sql->orWhere('chat_participants.user_id', $user_ids[$i]);
                    }
                }
            })
            ->whereNotNull("chats.temporary_chat")
            ->groupBy('chats.id')
            ->select('chats.id')
            ->pluck('chats.id')
            ->toArray();

        ChatParticipant::whereIn('chat_id', $usersChatsIds)->delete();

        Chat::where('id', $usersChatsIds)->delete();
    }


    public function test_message()
    {
        // $user_ids = [1, 2];
        // // $chat = new Chat();
        // $usersChatsIds = Chat::leftJoin('chat_participants', 'chats.id', 'chat_participants.chat_id')
        //     ->where(function ($sql) use ($user_ids) {
        //         for ($i = 0; $i < count($user_ids); $i++) {
        //             if ($i == 0) {
        //                 $sql->where('chat_participants.user_id', $user_ids[$i]);
        //             } else {
        //                 $sql->orWhere('chat_participants.user_id', $user_ids[$i]);
        //             }
        //         }
        //     })
        //     ->whereNotNull("chats.temporary_chat")
        //     ->groupBy('chats.id')
        //     ->select('chats.id')
        //     ->pluck('chats.id')
        //     ->toArray();

        // return response()->json(['data' => $usersChatsIds]);
    }
}
