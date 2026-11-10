<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromotionProduct extends Model
{
    protected $fillable = [
        'code',
        'name',
        'placement',
        'duration_hours',
        'currency',
        'price_minor',
        'active',
    ];

    protected $casts = [
        'duration_hours' => 'integer',
        'price_minor' => 'integer',
        'active' => 'boolean',
    ];

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }
}
