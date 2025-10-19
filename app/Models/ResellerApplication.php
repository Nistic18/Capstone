<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_address',
        'business_name',
        'address',
        'country',
        'province',
        'city',
        'zip_code',
        'business_license_id',
        'phone_number',
        'pdf_file',
        'status',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}