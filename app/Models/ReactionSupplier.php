<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReactionSupplier extends Model
{
    protected $table = 'supplierreactions';
    protected $fillable = ['user_id', 'post_id', 'type'];

    public function post()
    {
        return $this->belongsTo(PostSupplier::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
