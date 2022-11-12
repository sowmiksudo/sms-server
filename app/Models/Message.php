<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public $fillable = [
        'api_id',
        'channel_id',
        'number',
        'message',
        'delivered',
    ];

    public $casts = [
        'delivered' => 'boolean'
    ];
}
