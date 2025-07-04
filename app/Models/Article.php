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

    /**
     * Scope to select summary fields for listing.
     */
    public function scopeSummaryFields($query)
    {
        return $query->select([
            'id',
            'title',
            'description',
            'image_url',
            'source',
            'author',
            'category',
            'published_at',
        ]);
    }

    /**
     * Scope to order articles by latest published date.
     */
    public function scopeLatestPublished($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Scope to filter articles based on user preferences.
     */
    public function scopeMatchPreferences($query, $preferences)
    {
        if ($preferences->sources) {
            $query->whereIn('source', $preferences->sources);
        }

        if ($preferences->categories) {
            $query->whereIn('category', $preferences->categories);
        }

        if ($preferences->authors) {
            $query->whereIn('author', $preferences->authors);
        }

        return $query;
    }

    public static function defaultPerPage()
    {
        return config('pagination.per_page');
    }
}
