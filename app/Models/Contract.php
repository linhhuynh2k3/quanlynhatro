<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'listing_id',
        'landlord_id',
        'tenant_id',
        'start_date',
        'end_date',
        'monthly_price',
        'deposit_amount',
        'status',
        'approval_status',
        'reserved_at',
        'payment_status',
        'paid_at',
        'payment_id',
        'signature_name',
        'signature_data',
        'terms_accepted_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'paid_at' => 'datetime',
        'reserved_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function utilityBills()
    {
        return $this->hasMany(UtilityBill::class);
    }
}
