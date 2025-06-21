<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'content',
        'url',
        'image_url',
        'source',
        'author',
        'category',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Scope for keyword search using FULLTEXT index.
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->whereRaw(
            'MATCH(title, description, content) AGAINST (? IN BOOLEAN MODE)',
            [$keyword]
        );
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by source.
     */
    public function scopeSource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope to filter by published date.
     */
    public function scopePublishedOn($query, $date)
    {
        return $query->whereDate('published_at', $date);
    }
}
