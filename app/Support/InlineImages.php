<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Images pasted into the rich editor can arrive as base64 data URLs, which bloat the
 * article body by megabytes. This moves each one into a file on the public disk and
 * points the HTML at that file instead.
 */
class InlineImages
{
    public const DIRECTORY = 'content';

    private const PATTERN = '#data:image/(png|jpe?g|gif|webp);base64,([A-Za-z0-9+/=]+)#i';

    private const EXTENSIONS = ['jpeg' => 'jpg'];

    public static function extract(?string $html): ?string
    {
        if ($html === null || ! str_contains($html, 'data:image/')) {
            return $html;
        }

        return preg_replace_callback(self::PATTERN, function (array $match) {
            $bytes = base64_decode($match[2], true);

            // Leave anything that is not a real image untouched.
            if ($bytes === false || @getimagesizefromstring($bytes) === false) {
                return $match[0];
            }

            $extension = strtolower($match[1]);
            $extension = self::EXTENSIONS[$extension] ?? $extension;
            $path = self::DIRECTORY.'/'.sha1($bytes).'.'.$extension;

            $disk = Storage::disk('public');
            if (! $disk->exists($path)) {
                $disk->put($path, $bytes);
            }

            return '/storage/'.$path;
        }, $html);
    }
}
