<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use FlushesSiteCache;

    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'status'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function url(): string
    {
        return route('content.show', $this->slug);
    }

    public function getSafeBodyAttribute(): string
    {
        return clean($this->body);
    }
}
