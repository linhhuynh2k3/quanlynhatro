<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityBill extends Model
{
    protected $fillable = [
        'contract_id',
        'listing_id',
        'landlord_id',
        'tenant_id',
        'bill_number',
        'bill_date',
        'due_date',
        'status',
        'room_price',
        'electricity_old_reading',
        'electricity_new_reading',
        'electricity_usage',
        'electricity_price_per_unit',
        'electricity_total',
        'water_old_reading',
        'water_new_reading',
        'water_usage',
        'water_price_per_unit',
        'water_total',
        'wifi_price',
        'garbage_price',
        'total_amount',
        'payment_status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'room_price' => 'decimal:2',
        'electricity_price_per_unit' => 'decimal:2',
        'electricity_total' => 'decimal:2',
        'water_price_per_unit' => 'decimal:2',
        'water_total' => 'decimal:2',
        'wifi_price' => 'decimal:2',
        'garbage_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'bill_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

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

}
