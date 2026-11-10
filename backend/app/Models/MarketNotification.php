<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MarketNotification extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'type',
        'data_json',
        'read_at',
        'created_at',
    ];

    protected $casts = [
        'data_json' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (MarketNotification $n) {
            if (empty($n->id)) {
                $n->id = (string) Str::uuid();
            }
            if (empty($n->created_at)) {
                $n->created_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
