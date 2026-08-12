<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'priority',
        'link',
        'source',
        'is_read',
        'payload',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'payload' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
