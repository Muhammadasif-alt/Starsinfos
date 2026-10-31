<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SeoSection
{
    public static function make(): Section
    {
        return Section::make('SEO')
            ->collapsed()
            ->schema([
                TextInput::make('meta_title')->maxLength(255),
                Textarea::make('meta_description')->rows(2)->maxLength(500),
            ]);
    }
}
