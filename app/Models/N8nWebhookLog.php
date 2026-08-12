<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class N8nWebhookLog extends Model
{
    protected $fillable = [
        'source',
        'event',
        'channel',
        'payload',
        'message',
        'customer',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
