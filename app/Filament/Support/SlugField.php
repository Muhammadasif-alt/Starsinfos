<?php

namespace App\Filament\Support;

use App\Models\Category;
use App\Models\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class SlugField
{
    /** URLs the catch-all content routes must never claim. */
    public const RESERVED = ['admin', 'blog', 'search', 'subscribe', 'contact', 'sitemap', 'robots', 'livewire', 'filament', 'storage', 'build'];

    public static function title(string $name = 'title'): TextInput
    {
        return TextInput::make($name)
            ->required()
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                if ($operation === 'create') {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    public static function make(): TextInput
    {
        return TextInput::make('slug')
            ->required()
            ->maxLength(255)
            ->alphaDash()
            ->regex('/^[a-z0-9-]+$/')
            ->unique(ignoreRecord: true);
    }

    /** For categories and pages, which share the first URL segment. */
    public static function topLevel(string $otherModel): TextInput
    {
        return self::make()->rules([fn () => function (string $attribute, mixed $value, \Closure $fail) use ($otherModel) {
            if (in_array($value, self::RESERVED, true) || $otherModel::where('slug', $value)->exists()) {
                $fail('This slug is reserved or already used by another category or page.');
            }
        }]);
    }

    /** Subcategory archives win over posts in /{category}/{slug}, so a post cannot share their slug. */
    public static function forPost(): TextInput
    {
        return self::make()->rules([fn () => function (string $attribute, mixed $value, \Closure $fail) {
            if (Category::where('slug', $value)->exists()) {
                $fail('This slug is already used by a category.');
            }
        }]);
    }
}
