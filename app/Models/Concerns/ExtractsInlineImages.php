<?php

namespace App\Models\Concerns;

use App\Support\InlineImages;

trait ExtractsInlineImages
{
    protected static function bootExtractsInlineImages(): void
    {
        static::saving(function (self $model) {
            if ($model->isDirty('body')) {
                $model->body = InlineImages::extract($model->body);
            }
        });
    }
}
