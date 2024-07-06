<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Events\ChatNotifyEvent;
use App\Events\VideoStreamEvent;
use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\IceCandidate;
use App\Models\Room;
use App\Traits\ResponsesTrait;
use Exception;
use Illuminate\Http\Request;

class VideoStreamController extends Controller
{
    use ResponsesTrait;

    public function start_video_chat(Request $request)
    {
        // check first maybe someone else started that chat but user 
        // didn't handle that
        try {
            $chat = Chat
                ::where('id', $request->get("chat_id"))
                ->where("chat_uuid", $request->get("chat_uuid"))
                ->first();

            if (!$chat) {
                // creation of chat here again if it's not exist
            }

            $all_chats_rooms = Room::where('chat_id', $chat->id)->whereNull('answer')->pluck('id');

            IceCandidate::whereIn('room_id', $all_chats_rooms)->delete();

            Room::whereIn('id', $all_chats_rooms)->delete();

            $chat->update([
                'video_chat_streaming' => true,
            ]);

            $find_participant = ChatParticipant::where([
                'chat_id' => $chat->id,
                'user_id' => $request->get('user_id'),
            ])->first();

            if ($find_participant) {
                $find_participant->update([
                    'in_video_stream' => true,
                ]);
            } else {
                $find_participant = ChatParticipant::create([
                    'chat_id' => $chat->id,
                    'user_id' => $request->get('user_id'),
                    'in_video_stream' => true,
                    'participate_at' => date('Y-m-d H:i:s'),
                ]);
            }


            return $this->success();

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

    }

    // for specific new chat participant
    public function videochat_entrance(Request $request)
    {
        // first check whether video chat is already finished
        // if it's finished send to user a response with a message "video chat is over" 
    }

    public function leave_video_chat(Request $request)
    {
        // after every exit of user send to chat notification with pusher that participant left the chat
        // and also after every user exit minus the chat participant qty and set null in the end

        $chat = Chat
            ::where('id', $request->get("chat_id"))
            ->where("chat_uuid", $request->get("chat_uuid"))
            ->first();

        if (!$chat) {
            return $this->success();
        }

        $all_chats_rooms = Room::where('chat_id', $chat->id)->whereNull('answer')->pluck('id');

        IceCandidate::whereIn('room_id', $all_chats_rooms)->delete();

        Room::whereIn('id', $all_chats_rooms)->delete();

        $chat->update([
            'video_chat_streaming' => null,
        ]);

        ChatParticipant::where([
            'chat_id' => $chat->id,
            'user_id' => $request->get('user_id'),
        ])
            ->update(['in_video_stream' => null]);

        $chat_controller = new ChatController();

        broadcast(new ChatMessageEvent(null, $chat, true));

        $chat_controller->notify_all_users_channels_listener($chat, $request);

        return $this->success();
    }


    // remember to change Kernel.php file :

    //  'api' => [
    //     'throttle:300,1',
    //     \Illuminate\Routing\Middleware\SubstituteBindings::class,
    //  ],

    // in order to accept more request per minute
    public function video_stream(Request $request)
    {
        // handle video data to broadcast
        $chat_participant = ChatParticipant
            ::where("user_id", $request->get("user_id"))
            ->where("chat_id", $request->get("chat_id"))
            ->with("user", 'chat')
            ->first();

        $chat = Chat::where("id", $request->get("chat_id"))
            ->where("chat_uuid", $request->get("chat_uuid")) // for double checking
            ->first();

        event(
            new VideoStreamEvent(
                $chat_participant,
                $chat,
                $request->get("image_data")
            )
        );

        $channel_name = "video_" . CHAT_CHANNEL_ID . "{$chat->id}" . CHAT_CHANNEL_UUID . "{$chat->chat_uuid}";

        return $this->success(["channel_name" => $channel_name]);
    }

}
