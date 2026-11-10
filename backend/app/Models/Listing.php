<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ListingAttributeValue::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'listing_media')
            ->withPivot(['sort_order', 'is_cover'])
            ->orderByPivot('sort_order');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ListingStatusHistory::class);
    }

    /**
     * Audit and record status change.
     */
    public function recordStatusTransition(string $from, string $to, ?User $actor = null, ?string $reason = null): void
    {
        ListingStatusHistory::create([
            'listing_id' => $this->id,
            'actor_id' => $actor?->id,
            'from_status' => $from,
            'to_status' => $to,
            'reason' => $reason,
            'created_at' => Carbon::now(),
        ]);
    }

    public function transitionTo(string $targetStatus, ?User $actor = null, ?string $reason = null): bool
    {
        $allowedTransitions = [
            'draft' => ['pending_review'],
            'pending_review' => ['active', 'rejected'],
            'active' => ['paused', 'sold', 'expired', 'removed'],
            'paused' => ['active', 'pending_review'],
            'rejected' => ['draft'],
            'expired' => ['pending_review'],
        ];

        if (!isset($allowedTransitions[$this->status]) || !in_array($targetStatus, $allowedTransitions[$this->status])) {
            return false;
        }

        $from = $this->status;
        $this->status = $targetStatus;

        if ($targetStatus === 'active') {
            $this->published_at = Carbon::now();
            $this->expires_at = Carbon::now()->addDays(30);
            $this->moderation_status = 'approved';
        } elseif ($targetStatus === 'sold') {
            $this->sold_at = Carbon::now();
        }

        $this->save();
        $this->recordStatusTransition($from, $targetStatus, $actor, $reason);

        return true;
    }
}
