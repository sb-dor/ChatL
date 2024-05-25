<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatNotifyEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    private $channel_name;
    private Chat $chat;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chat, $user_id)
    {
        $this->channel_name = CHANNEL_NOTIFY_OF_USER . "{$user_id}";
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [$this->channel_name]; //name of channel 
    }

    public function broadcastAs()
    {
        return 'chat_notification.event'; //name of event 
    }

    public function broadcastWith()
    {
        return  [
            'chat' =>$this->chat,
        ]; //after working any fun that calling this event will send message reverse
    }
}
