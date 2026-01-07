<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'latitude',
        'longitude',
        'address',
        'phone',
        'email_verified_at',
        // BAN FIELDS - ADD THESE THREE LINES
        'is_banned',
        'banned_until',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // BAN CASTS - ADD THESE TWO LINES
            'is_banned' => 'boolean',
            'banned_until' => 'datetime',
        ];
    }

    // Add this accessor to check if user is admin
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function resellerApplications()
    {
        return $this->hasMany(ResellerApplication::class, 'user_id');
    }

    public function latestResellerApplication()
    {
        return $this->hasOne(ResellerApplication::class, 'user_id')->latestOfMany();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * Check if user is currently banned
     */
    public function isBanned()
    {
        if (!$this->is_banned) {
            return false;
        }

        // If banned_until is null, it's a permanent ban
        if (is_null($this->banned_until)) {
            return true;
        }

        // If banned_until has passed, user is no longer banned
        if (now()->greaterThan($this->banned_until)) {
            // Auto-unban if restriction expired
            $this->update([
                'is_banned' => false,
                'banned_until' => null,
                'ban_reason' => null,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Ban history relationship
     */
    public function banHistory()
    {
        return $this->hasMany(UserBanHistory::class, 'user_id');
    }

    /**
     * Who banned this user
     */
    public function bannedByUser()
    {
        return $this->hasOne(UserBanHistory::class, 'user_id')->latest();
    }
}