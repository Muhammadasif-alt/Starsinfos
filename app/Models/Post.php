<?php

namespace App\Models;

use App\Models\Concerns\ExtractsInlineImages;
use App\Models\Concerns\FlushesSiteCache;
use App\Models\Concerns\HasPublication;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use ExtractsInlineImages, FlushesSiteCache, HasFactory, HasPublication;

    public const JOB_TYPES = ['Permanent', 'Contract', 'Internship'];

    protected $fillable = [
        'category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'featured_image',
        'meta_title', 'meta_description', 'tags', 'faqs', 'source_url', 'department', 'location', 'job_type', 'application_status', 'status', 'is_featured',
        'is_trending', 'is_outdated', 'closing_date', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'faqs' => 'array',
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

    /** Open or Closed for schemes: a past closing date wins over the stored status. */
    public function getApplicationLabelAttribute(): ?string
    {
        if ($this->closing_date?->isBefore(today())) {
            return 'Closed';
        }

        return $this->application_status;
    }

    public function getClosingLabelAttribute(): string
    {
        if (! $this->closing_date) {
            return 'Open';
        }

        return $this->closing_date->isBefore(today()) ? 'Closed' : 'Last: '.$this->closing_date->format('j M');
    }
}
