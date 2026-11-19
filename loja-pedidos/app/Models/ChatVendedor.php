<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatVendedor extends Model
{
    protected $table = 'chats_vendedor';

    protected $fillable = ['chat_id', 'nome', 'tipo'];
}
