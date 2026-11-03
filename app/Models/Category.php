<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use FlushesSiteCache, HasFactory;

    /** Root categories whose archives and posts must show the disclaimer. */
    private const DISCLAIMER_SLUGS = ['government-schemes'];

    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'color', 'icon', 'image', 'sort_order'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->posts()->published();
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    public function root(): self
    {
        return $this->parent ?? $this;
    }

    /** Own id plus its subcategories, for archives that include child posts. */
    public function familyIds(): array
    {
        return $this->children()->pluck('id')->push($this->id)->all();
    }

    public function url(): string
    {
        return $this->parent
            ? route('content.nested', [$this->parent->slug, $this->slug])
            : route('content.show', $this->slug);
    }

    public function showsDisclaimer(): bool
    {
        return in_array($this->root()->slug, self::DISCLAIMER_SLUGS, true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}
