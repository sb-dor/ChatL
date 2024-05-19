<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    private ChatMessage $message;
    private $CHANNEL_NAME;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $chat_uuid)
    {
        $this->message = $message;
        $this->CHANNEL_NAME = CHAT_CHANNEL_ID . "{$message->chat_id}" . CHAT_CHANNEL_UUID . "{$chat_uuid}";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ["{$this->CHANNEL_NAME}"];
    }

    public function broadcastAs()
    {
        return 'chat_messages.event'; //name of event 
    }

    public function broadcastWith()
    {
        return  [
            'message' =>$this->message,
        ]; //after working any fun that calling this event will send message reverse
    }
}
