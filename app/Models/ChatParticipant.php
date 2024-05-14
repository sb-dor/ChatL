<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'chat_participants';

    protected $guarded = ['id'];
}
