<?php

namespace App\Events;

use App\Models\Chat;
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

    private $CHANNEL_NAME;
    private $sendingMessage = null;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $chat, $init_chat = false)
    {
        $this->CHANNEL_NAME = CHAT_CHANNEL_ID . "{$chat->id}" . CHAT_CHANNEL_UUID . "{$chat->chat_uuid}";

        $this->sendingMessage = [
            'message' => $message,
        ];

        if ($init_chat) {
            $this->sendingMessage['chat'] = $chat;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [$this->CHANNEL_NAME];
    }

    public function broadcastAs()
    {
        return 'chat_messages.event'; //name of event 
    }

    public function broadcastWith()
    {
        return $this->sendingMessage;// after working any fun that calling this event will send message reverse
    }
}
