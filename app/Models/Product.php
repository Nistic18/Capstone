<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'quantity', 
        'user_id', 
        'image', 
        'status', 
        'low_stock_threshold',
        'product_category_id',
        'product_type_id',
        'unit_type',
        'unit_value'
    ];

    protected $casts = [
        'low_stock_threshold' => 'integer',
        'quantity' => 'integer',
        'unit_value' => 'decimal:2',
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

    // Unit Display Helper
    public function getUnitDisplay()
    {
        if (!$this->unit_type || !$this->unit_value) {
            return '';
        }

        switch ($this->unit_type) {
            case 'pack':
                return $this->unit_value . ' pieces per pack';
            case 'kilo':
                return $this->unit_value . ' kg';
            case 'box':
                return $this->unit_value . ' kg per box';
            case 'piece':
                return 'per piece';
            default:
                return '';
        }
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

    public function type()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}