<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
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
                //
            }

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

        $chat->update([
            'video_chat_streaming' => null,
        ]);

        ChatParticipant::where([
            'chat_id' => $chat->id,
            'user_id' => $request->get('user_id'),
        ])
            ->update(['in_video_stream' => null]);

        return $this->success();
    }


    public function video_stream(Request $request)
    {
        // handle video data to broadcast
    }

}
