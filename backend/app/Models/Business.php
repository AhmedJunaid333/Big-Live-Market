<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Business extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'logo_media_id',
        'banner_media_id',
        'verification_status',
        'status',
        'listing_quota',
    ];

    protected $casts = [
        'listing_quota' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Business $b) {
            if (empty($b->slug)) {
                $b->slug = Str::slug($b->name) . '-' . Str::random(6);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function isMember(int $userId): bool
    {
        return $this->owner_id === $userId || $this->members()->where('user_id', $userId)->exists();
    }
}
