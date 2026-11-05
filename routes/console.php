<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('content:extract-images', function () {
    foreach ([\App\Models\Post::class, \App\Models\BlogPost::class, \App\Models\Page::class] as $model) {
        $model::query()->where('body', 'like', '%data:image/%')->each(function ($record) use ($model) {
            $before = strlen($record->body);
            $record->body = \App\Support\InlineImages::extract($record->body);
            $record->saveQuietly();
            $this->info(class_basename($model)." #{$record->id} {$record->slug}: ".round($before / 1024).' KB -> '.round(strlen($record->body) / 1024).' KB');
        });
    }
    \App\Support\SiteCache::flush();
})->purpose('Move base64 images pasted into article bodies into files on the public disk');
