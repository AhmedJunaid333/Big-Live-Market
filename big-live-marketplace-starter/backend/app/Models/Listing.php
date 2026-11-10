<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Listing extends Model {
    protected $fillable = ['seller_id','category_id','city_id','title','description','price_minor','currency','condition_code','status'];
    protected $casts = ['price_minor'=>'integer'];
    public function seller(): BelongsTo { return $this->belongsTo(User::class, 'seller_id'); }
}
