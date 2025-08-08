<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SupplierProduct extends Model
{
    protected $fillable = ['name', 'description', 'price', 'quantity', 'user_id', 'image', 'status'];

    // Each product belongs to one user (supplier)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function isAvailable()
    {
        return $this->status === 'available';
    }

}
