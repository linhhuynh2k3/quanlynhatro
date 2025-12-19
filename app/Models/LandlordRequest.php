<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'latitude',
        'longitude',
        'message',
        'password',
        'cccd_number',
        'cccd_front_image',
        'cccd_back_image',
        'business_license_image',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
