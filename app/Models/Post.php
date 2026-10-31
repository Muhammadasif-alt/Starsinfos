<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use App\Models\Concerns\HasPublication;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use FlushesSiteCache, HasFactory, HasPublication;

    protected $fillable = [
        'category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'featured_image',
        'meta_title', 'meta_description', 'source_url', 'status', 'is_featured',
        'is_trending', 'is_outdated', 'closing_date', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'is_outdated' => 'boolean',
            'closing_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function url(): string
    {
        return route('content.nested', [$this->category->root()->slug, $this->slug]);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->featured_image ? Storage::disk('public')->url($this->featured_image) : null;
    }

    public function getClosingLabelAttribute(): string
    {
        return $this->closing_date ? 'Last: '.$this->closing_date->format('j M') : 'Open';
    }
}
