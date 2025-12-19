<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'credited_user_id',
        'type',
        'listing_id',
        'contract_id',
        'amount',
        'method',
        'status',
        'transaction_code',
        'meta',
        'description',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function creditedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'credited_user_id');
    }
}
