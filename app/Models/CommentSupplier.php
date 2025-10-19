<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentSupplier extends Model
{
    protected $table = 'suppliercomments';
    protected $fillable = ['user_id', 'post_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(PostSupplier::class, 'post_id');
    }
}
