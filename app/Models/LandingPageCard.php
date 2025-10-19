<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageCard extends Model
{
    protected $fillable = ['landing_page_content_id', 'section', 'title', 'content', 'image', 'order'];

    public function section()
    {
        return $this->belongsTo(LandingPageContent::class, 'landing_page_content_id');
    }
}
