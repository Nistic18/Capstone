<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'total_price', 
        'delivery_fee',  
        'status', 
        'refund_status', 
        'refund_reason'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'product_status') // include status
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Optional: helper to get total including delivery fee
    public function getTotalWithDeliveryAttribute()
    {
        return $this->total_price + $this->delivery_fee;
    }
}
