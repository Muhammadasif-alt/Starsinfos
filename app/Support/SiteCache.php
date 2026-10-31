<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class SiteCache
{
    public const TTL = 600;

    public const HOME = 'site.home';

    public const LAYOUT = 'site.layout';

    public const SETTINGS = 'site.settings';

    public static function flush(): void
    {
        Cache::forget(self::HOME);
        Cache::forget(self::LAYOUT);
        Cache::forget(self::SETTINGS);
    }
}
