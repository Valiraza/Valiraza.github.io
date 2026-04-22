<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'category',
        'status',
        'admin_replies',
        'replied_at',
        'message',
    ];

    protected $casts = [
        'admin_replies' => 'array',
        'replied_at' => 'datetime',
    ];
}
