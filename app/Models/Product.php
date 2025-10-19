<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'quantity', 'user_id', 'image', 'status', 'low_stock_threshold'];

    protected $casts = [
        'low_stock_threshold' => 'integer',
        'quantity' => 'integer',
    ];

    // Each product belongs to one user (Reseller)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Inventory Management Relations
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class)->orderBy('created_at', 'desc');
    }

    // Inventory Management Methods
    public function isLowStock()
    {
        $threshold = $this->low_stock_threshold ?? 10;
        return $this->quantity <= $threshold && $this->quantity > 0;
    }

    public function isOutOfStock()
    {
        return $this->quantity <= 0;
    }

    public function addStock($quantity, $reason = 'restock', $notes = null)
    {
        $oldQuantity = $this->quantity;
        $this->quantity += $quantity;
        $this->save();

        $this->inventoryLogs()->create([
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => $quantity,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $this->quantity,
            'reason' => $reason,
            'notes' => $notes,
        ]);

        return $this;
    }

    public function removeStock($quantity, $reason = 'sale', $notes = null)
    {
        $oldQuantity = $this->quantity;
        $this->quantity = max(0, $this->quantity - $quantity);
        $this->save();

        $this->inventoryLogs()->create([
            'user_id' => auth()->id(),
            'type' => 'out',
            'quantity' => $quantity,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $this->quantity,
            'reason' => $reason,
            'notes' => $notes,
        ]);

        return $this;
    }

    public function setStock($quantity, $reason = 'adjustment', $notes = null)
    {
        $oldQuantity = $this->quantity;
        $difference = $quantity - $oldQuantity;
        $this->quantity = $quantity;
        $this->save();

        $this->inventoryLogs()->create([
            'user_id' => auth()->id(),
            'type' => $difference >= 0 ? 'in' : 'out',
            'quantity' => abs($difference),
            'old_quantity' => $oldQuantity,
            'new_quantity' => $this->quantity,
            'reason' => $reason,
            'notes' => $notes,
        ]);

        return $this;
    }

    public function getStockStatus()
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusBadge()
    {
        $status = $this->getStockStatus();
        
        switch ($status) {
            case 'out_of_stock':
                return '<span class="badge bg-danger">Out of Stock</span>';
            case 'low_stock':
                return '<span class="badge bg-warning">Low Stock</span>';
            default:
                return '<span class="badge bg-success">In Stock</span>';
        }
    }
}