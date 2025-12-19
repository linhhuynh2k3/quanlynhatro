<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'contract_id',
        'landlord_id',
        'tenant_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'period_start',
        'period_end',
        'rent_amount',
        'electricity_old_reading',
        'electricity_new_reading',
        'electricity_unit_price',
        'electricity_amount',
        'water_old_reading',
        'water_new_reading',
        'water_unit_price',
        'water_amount',
        'wifi_amount',
        'trash_amount',
        'other_services_amount',
        'other_services_note',
        'total_amount',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'rent_amount' => 'decimal:2',
        'electricity_unit_price' => 'decimal:2',
        'electricity_amount' => 'decimal:2',
        'water_unit_price' => 'decimal:2',
        'water_amount' => 'decimal:2',
        'wifi_amount' => 'decimal:2',
        'trash_amount' => 'decimal:2',
        'other_services_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || ($this->status === 'pending' && $this->due_date < now());
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    protected static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastInvoice = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return 'INV-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
