<?php

namespace App\Models\Concerns;

use App\Support\SiteCache;

trait FlushesSiteCache
{
    protected static function bootFlushesSiteCache(): void
    {
        static::saved(fn () => SiteCache::flush());
        static::deleted(fn () => SiteCache::flush());
    }
}
