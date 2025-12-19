<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_resolved',
        'resolved_at',
        'admin_note',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
