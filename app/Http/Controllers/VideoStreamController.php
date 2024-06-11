<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoStreamController extends Controller
{

    public function start_video_chat(Request $request)
    {
        // check first maybe someone else started that chat but user 
        // didn't handle that
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
    }


    public function video_stream(Request $request)
    {
        // handle video data to broadcast
    }

}
