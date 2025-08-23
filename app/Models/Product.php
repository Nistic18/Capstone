<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'quantity', 'user_id', 'image', 'status'];

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


}
