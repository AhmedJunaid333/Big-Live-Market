<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'seller_id',
        'category_id',
        'city_id',
        'title',
        'slug',
        'description',
        'price_minor',
        'currency',
        'negotiable',
        'condition_code',
        'status',
        'moderation_status',
        'latitude',
        'longitude',
        'location_label',
        'address_private',
        'published_at',
        'expires_at',
        'sold_at',
        'views_count',
        'favorites_count',
    ];

    protected $casts = [
        'price_minor' => 'integer',
        'negotiable' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'sold_at' => 'datetime',
        'views_count' => 'integer',
        'favorites_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            if (empty($listing->uuid)) {
                $listing->uuid = (string) Str::uuid();
            }
            if (empty($listing->slug)) {
                $listing->slug = Str::slug($listing->title) . '-' . substr($listing->uuid, 0, 8);
            }
            if (empty($listing->currency)) {
                $listing->currency = 'PKR';
            }
            if (empty($listing->status)) {
                $listing->status = 'draft';
            }
            if (empty($listing->moderation_status)) {
                $listing->moderation_status = 'pending';
            }
        });
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
