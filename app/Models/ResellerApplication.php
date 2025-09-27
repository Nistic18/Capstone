<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'valid_id_path',
    'business_path',
    'other_doc_path',
    'status',
    'rejection_reason',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

