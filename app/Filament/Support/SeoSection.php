<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SeoSection
{
    public static function make(): Section
    {
        return Section::make('SEO, Tags & FAQs')
            ->collapsed()
            ->schema([
                TextInput::make('meta_title')->maxLength(255),
                Textarea::make('meta_description')->rows(2)->maxLength(500)->helperText('Aim for 120 to 155 characters.'),
                TagsInput::make('tags')->helperText('Short search phrases, e.g. Islamabad Police Jobs.'),
                Repeater::make('faqs')->label('FAQs')->defaultItems(0)->collapsible()->schema([
                    TextInput::make('question')->required()->maxLength(255),
                    Textarea::make('answer')->required()->rows(2),
                ]),
            ]);
    }
}
