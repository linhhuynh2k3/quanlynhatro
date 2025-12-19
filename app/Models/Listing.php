<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'address',
        'province',
        'district',
        'ward',
        'area',
        'price',
        'phone',
        'images',
        'status',
        'is_featured',
        'approved_at',
        'expired_at',
        'views',
        'total_units',
        'available_units',
        'latitude',
        'longitude',
        'duration_days',
        'payment_type',
        'listing_price',
        'is_paid',
        'electricity_price',
        'water_price',
        'wifi_price',
        'garbage_price',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'expired_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_paid' => 'boolean',
        'listing_price' => 'decimal:2',
        'electricity_price' => 'decimal:2',
        'water_price' => 'decimal:2',
        'wifi_price' => 'decimal:2',
        'garbage_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            $listing->slug = static::generateUniqueSlug($listing->title);
        });

        static::updating(function (Listing $listing) {
            if ($listing->isDirty('title')) {
                $listing->slug = static::generateUniqueSlug($listing->title, $listing->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'listing';
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        return $slug;
    }
}
