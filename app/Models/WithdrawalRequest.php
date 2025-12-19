<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'fee',
        'net_amount',
        'status',
        'payout_method',
        'payout_details',
        'notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'payout_details' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}

