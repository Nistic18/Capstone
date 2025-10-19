<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'old_quantity',
        'new_quantity',
        'reason',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'old_quantity' => 'integer',
        'new_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabel()
    {
        return $this->type === 'in' ? 'Stock In' : 'Stock Out';
    }

    public function getTypeBadgeClass()
    {
        return $this->type === 'in' ? 'success' : 'danger';
    }
}