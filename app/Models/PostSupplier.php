<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostSupplier extends Model
{
    protected $table = 'supplierposts'; // ✅ Fix table name

    protected $fillable = ['user_id', 'title', 'content', 'image', 'status','is_featured',];
    
    protected $casts = [
    'is_featured' => 'boolean',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(ReactionSupplier::class, 'post_id');
    }

    public function userReaction($userId)
    {
        return $this->reactions()->where('user_id', $userId)->first();
    }

    public function comments()
    {
        return $this->hasMany(CommentSupplier::class, 'post_id')->latest();
    }
}
