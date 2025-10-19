<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageContent extends Model
{
    protected $fillable = ['section', 'title', 'content', 'image'];
    public function cards()
{
    return $this->hasMany(LandingPageCard::class);
}

}
