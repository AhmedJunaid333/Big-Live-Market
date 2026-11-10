<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryAttribute extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'label',
        'data_type',
        'required',
        'filterable',
        'options_json',
        'validation_json',
        'sort_order',
    ];

    protected $casts = [
        'required' => 'boolean',
        'filterable' => 'boolean',
        'options_json' => 'array',
        'validation_json' => 'array',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
