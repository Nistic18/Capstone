<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBanHistory extends Model
{
    use HasFactory;

    protected $table = 'user_ban_history';

    protected $fillable = [
        'user_id',
        'banned_by',
        'action_type',
        'reason',
        'banned_until',
    ];

    protected $casts = [
        'banned_until' => 'datetime',
    ];

    /**
     * Get the user that was banned
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin who performed the ban
     */
    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Check if this is a permanent ban
     */
    public function isPermanent()
    {
        return $this->action_type === 'ban' && is_null($this->banned_until);
    }

    /**
     * Check if this is a temporary restriction
     */
    public function isRestriction()
    {
        return $this->action_type === 'restrict';
    }
}