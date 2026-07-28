<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'status',
        'category',
        'tags',
        'seo_title',
        'seo_description',
        'author_id',
        'published_at'
    ];
    protected $casts = ['tags' => 'array', 'published_at' => 'datetime'];
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
