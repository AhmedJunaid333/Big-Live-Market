<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingAttributeValue extends Model
{
    protected $fillable = [
        'listing_id',
        'category_attribute_id',
        'value_text',
        'value_number',
        'value_boolean',
        'value_json',
    ];

    protected $casts = [
        'value_number' => 'float',
        'value_boolean' => 'boolean',
        'value_json' => 'array',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CategoryAttribute::class, 'category_attribute_id');
    }
}
