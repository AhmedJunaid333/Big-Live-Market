<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'code',
        'name',
        'currency',
        'price_minor',
        'billing_period',
        'listing_quota',
        'benefits_json',
        'active',
    ];

    protected $casts = [
        'price_minor' => 'integer',
        'listing_quota' => 'integer',
        'benefits_json' => 'array',
        'active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
