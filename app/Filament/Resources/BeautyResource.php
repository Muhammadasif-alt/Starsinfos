<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeautyResource\Pages;

class BeautyResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Publish';

    protected static ?string $navigationLabel = 'Beauty & Style';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'beauty-style';

    protected static ?string $rootSlug = 'beauty-style';

    protected static string $noun = 'beauty post';

    protected static function titleExample(): string
    {
        return 'e.g. 7 Skincare Tips for Pakistan\'s Hot Summers';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeautyPosts::route('/'),
            'create' => Pages\CreateBeautyPost::route('/create'),
            'edit' => Pages\EditBeautyPost::route('/{record}/edit'),
        ];
    }
}
