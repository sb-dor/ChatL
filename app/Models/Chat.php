<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $guarded = ['id'];


    public function chat_last_message()
    {
        return $this->hasOne(ChatMessage::class, 'chat_id', 'id')->latest('created_at');
    }
}
