<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'chats';

    protected $guarded = ['id'];


    public function chat_last_message()
    {
        return $this->hasOne(ChatMessage::class, 'chat_id', 'id')->latest('created_at');
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'chat_id', "id");
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'id');
    }

    public function chat_video_room()
    {
        return $this->hasOne(Room::class, 'chat_id', 'id');
    }
}
