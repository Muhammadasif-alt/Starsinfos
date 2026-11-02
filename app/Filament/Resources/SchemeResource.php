<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchemeResource\Pages;
use App\Models\Post;

class SchemeResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Publish';

    protected static ?string $navigationLabel = 'Government Schemes';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'government-schemes';

    protected static ?string $rootSlug = 'government-schemes';

    protected static string $noun = 'scheme';

    /** Number of open schemes, shown beside the menu item. */
    public static function getNavigationBadge(): ?string
    {
        $open = Post::published()->whereIn('category_id', static::familyIds())
            ->where(fn ($q) => $q->whereNull('closing_date')->orWhereDate('closing_date', '>=', today()))->count();

        return $open ?: null;
    }

    protected static function titleExample(): string
    {
        return 'e.g. Apni Chhat Apna Ghar: Interest-Free Home Loan';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchemes::route('/'),
            'create' => Pages\CreateScheme::route('/create'),
            'edit' => Pages\EditScheme::route('/{record}/edit'),
        ];
    }
}
