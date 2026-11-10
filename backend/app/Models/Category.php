<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'slug',
        'name',
        'icon',
        'sort_order',
        'active',
        'posting_fee_minor',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
        'posting_fee_minor' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(CategoryAttribute::class)->orderBy('sort_order');
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }
}
