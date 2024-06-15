<?php

namespace App\Events;

use App\Classes\VideoStreamClass;
use App\Models\Chat;
use App\Models\ChatParticipant;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoStreamEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    protected VideoStreamClass $video_stream_class;
    protected $channel_name;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        ChatParticipant $participant,
        Chat $chat,
        $video_stream_data,
    ) {
        $this->video_stream_class = new VideoStreamClass($participant, $video_stream_data);
        $this->channel_name = "video_" . CHAT_CHANNEL_ID . "{$chat->id}" . CHAT_CHANNEL_UUID . "{$chat->chat_uuid}";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [$this->channel_name];
    }

    public function broadcastAs()
    {
        return 'chat_video_stream.event'; // name of event 
    }

    public function broadcastWith()
    {
        return $this->video_stream_class->responseForPusher();
    }

}
