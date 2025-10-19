<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
protected $fillable = ['user_id', 'title', 'content', 'image', 'status'];


public function user()
{
    return $this->belongsTo(User::class);
}
public function reactions()
{
    return $this->hasMany(Reaction::class);
}

public function userReaction($userId)
{
    return $this->reactions()->where('user_id', $userId)->first();
}
public function comments()
{
    return $this->hasMany(Comment::class)->latest();
}

}
