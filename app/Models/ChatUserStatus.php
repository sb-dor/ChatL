<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatUserStatus extends Model
{
    use HasFactory;

    protected $table = 'chat_user_status';

    protected $guarded = ['id'];
}
