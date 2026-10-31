<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait SeedsImages
{
    /** Copies a bundled image to the public disk and returns its stored path. */
    protected function seedImage(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        $path = "seed/{$name}.jpg";

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, file_get_contents(database_path("seeders/images/{$name}.jpg")));
        }

        return $path;
    }
}
