<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use App\Models\Concerns\HasPublication;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    use FlushesSiteCache, HasFactory, HasPublication;

    private const WORDS_PER_MINUTE = 200;

    protected $fillable = [
        'author_id', 'category_id', 'title', 'slug', 'excerpt', 'body', 'featured_image',
        'meta_title', 'meta_description', 'status', 'is_featured', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $post) {
            $words = str_word_count(strip_tags($post->body));
            $post->read_time = max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function url(): string
    {
        return route('blog.show', $this->slug);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->featured_image ? Storage::disk('public')->url($this->featured_image) : null;
    }
}
