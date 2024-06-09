<?php

namespace App\Events;
use App\Models\ChatParticipant;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoStreamEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    protected ChatParticipant $chat_participant;
    protected $video_stream_data;
    protected $channel_name;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return ['video_steam_data' => $this->video_stream_data];
    }

}
