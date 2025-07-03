<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'user_id', 'image'];

    // Each product belongs to one user (supplier)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
