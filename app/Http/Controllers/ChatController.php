<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
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

        try {
            $user = $request->user();

            $chats = Chat
                ::join('chat_participants', 'chats.id', 'chat_participants.chat_id')
                ->where('chat_participants.user_id', $user->id)
                ->whereNull('temporary_chat')
                ->select('chats.*')
                ->with('chat_last_message')
                ->with([
                    'participants' => function ($sql) use ($user) {
                        $sql->with('user')->where('user_id', '!=', $user->id);
                    }
                ])
                ->get();

            return $this->success(['chats' => $chats]);
        } catch (Exception $e) {
            return $this->fail(['message' => $e->getMessage()]);
        }
    }


    public function get_user_chat_on_entrance(Request $request)
    {
        try {
            $current_user = $request->user();

            $user_ids = [];

            if ($request->get('chat_uuid')) {
                $user_ids = ChatParticipant
                    ::join('chats', 'chat_participants.chat_id', 'chats.id')
                    ->where('chats.chat_uuid', $request->get('chat_uuid'))
                    ->pluck('chat_participants.user_id')
                    ->toArray();
            } else if ($request->get('with_user_id')) {
                $user_ids = [$current_user->id, $request->get('with_user_id')];
            }

            // $findChat = Chat::where('chat_uuid', $request->get('chat_uuid'))->first();

            $findChat = $this->chat_finder(false, $user_ids, $request->get('chat_uuid'));

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

            $findChat = $findChat->load(
                [
                    'participants' => function ($sql) use ($current_user) {
                        $sql->with('user')->where('user_id', '!=', $current_user->id);
                    },
                    'messages' => function ($sql) {
                        $sql->with('user', 'related_to_user')
                            ->whereNull('deleted_at')
                            ->orderBy('created_at', 'desc')
                            ->take(15);
                    },
                    'chat_video_room' => function ($sql) {
                        $sql->orderBy('created_at', 'desc');
                    },
                ]
            );

            return $this->success(['chat' => $findChat]);
        } catch (Exception $e) {
            return $this->fail(['message' => $e->getMessage()]);
        }
    }


    public function message_handler(Request $request)
    {
        try {
            $chat = Chat::where('id', $request->get('chat_id'))->first();

            $message = ChatMessage::create([
                "chat_id" => $request->get("chat_id"),
                "user_id" => $request->get("user_id"),
                "related_to_user_id" => $request->get("related_to_user_id"),
                "chat_message_uuid" => $request->get('chat_message_uuid'),
                "message" => $request->get("message"),
                "created_at" => $request->get("created_at"),
            ]);

            if ($chat->temporary_chat) {
                Chat::where('id', $request->get('chat_id'))->update(['temporary_chat' => null]);
            }

            broadcast(new ChatMessageEvent($message, $chat));
            $this->notify_all_users_channels_listener($chat, $request);
        } catch (Exception $e) {
            return $this->fail(['message' => $e->getMessage()]);
        }
    }

    public function notify_all_users_channels_listener($chat, $request)
    {
        $chat = $chat->load([
            'chat_last_message',
            'participants' => function ($sql) {
                $sql->with('user');
            },
        ]);

        if ($chat->video_chat_streaming) {
            $chat->load([
                'chat_video_room' => function ($sql) {
                    $sql->orderBy('created_at', 'desc');
                }
            ]);
        }

        $find_all_chat_participants = ChatParticipant
            ::where('chat_id', $chat->id)
            ->groupBy('user_id')
            ->pluck('user_id')
            ->toArray();

        foreach ($find_all_chat_participants as $user_id) {
            broadcast(new ChatNotifyEvent($chat, $user_id));
        }
    }

    public function delete_temp_created_chats(Request $request)
    {
        Chat::where('id', $request->get('chat_id'))
            ->where('chat_uuid', $request->get('chat_uuid'))
            ->whereNotNull('temporary_chat')
            ->delete();

        ChatParticipant::whereNull('chat_id')->delete();

        return $this->success(['chat_id' => $request->get('chat_id'), "chat_uuid" => $request->get('chat_uuid')]);
    }

    // it's necessary, when user turned off the app without going back and the chat which was created temporary wasn't deleted
    private function find_and_delete_users_temporary_created_chat($user_ids = [])
    {

        $usersChatsIds = $this->chat_finder(true, $user_ids);

        ChatParticipant::whereIn('chat_id', $usersChatsIds)->delete();

        ChatParticipant::whereNull("chat_id")->delete();

        Chat::where('id', $usersChatsIds)->delete();
    }

    private function chat_finder($get_ids, $user_ids = [], $chat_uuid = null)
    {
        $foundIds = Chat::leftJoin('chat_participants', 'chats.id', '=', 'chat_participants.chat_id')
            ->whereIn('chat_participants.user_id', $user_ids)
            ->groupBy('chats.id')
            ->havingRaw('COUNT(chat_participants.user_id) = ?', [count($user_ids)])
            ->havingRaw('COUNT(DISTINCT chat_participants.user_id) = ?', [count($user_ids)]);

        if ($get_ids) {

            $foundIds = $foundIds
                ->whereNotNull("chats.temporary_chat")
                ->pluck('chats.id')
                ->toArray();

            if (count($foundIds) < 2)
                return [];

            return $foundIds;
        } else {

            $foundIds = $foundIds
                ->whereNull("chats.temporary_chat")
                ->pluck('chats.id')
                ->toArray();

            $findChat = Chat::whereIn('id', $foundIds)->first();

            if ($findChat)
                return $findChat;
        }
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

        $res = $this->chat_finder(false, [2, 3]);


        return $res;

        // return response()->json(['data' => $usersChatsIds]);
    }
}
