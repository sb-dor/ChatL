<?php

namespace App\Classes;

use App\Models\ChatParticipant;

class VideoStreamClass
{
    private ChatParticipant $chatParticipant;

    private $video_stream_data;

    public function __construct(
        ChatParticipant $chatParticipant,
        $video_stream_data
    ) {
        $this->chatParticipant = $chatParticipant;
        $this->video_stream_data = $video_stream_data;
    }

    public function responseForPusher()
    {
        return [
            "chat_participant" => $this->chatParticipant,
            "video_stream_data" => $this->video_stream_data,
        ];
    }
}