<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BispResource\Pages;
use App\Models\Post;

class BispResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Publish';

    protected static ?string $navigationLabel = 'BISP 8171';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'bisp';

    protected static ?string $rootSlug = 'bisp-8171';

    protected static string $noun = 'BISP update';

    protected static function titleExample(): string
    {
        return 'e.g. BISP 8171 Payment Update for November';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBisps::route('/'),
            'create' => Pages\CreateBisp::route('/create'),
            'edit' => Pages\EditBisp::route('/{record}/edit'),
        ];
    }
}
