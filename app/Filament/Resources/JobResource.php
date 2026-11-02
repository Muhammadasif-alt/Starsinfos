<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\Post;

class JobResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Publish';

    protected static ?string $navigationLabel = 'Jobs';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'jobs';

    protected static ?string $rootSlug = 'jobs';

    protected static string $noun = 'job';

    /** Number of open jobs, shown beside the menu item. */
    public static function getNavigationBadge(): ?string
    {
        $open = Post::published()->whereIn('category_id', static::familyIds())
            ->where(fn ($q) => $q->whereNull('closing_date')->orWhereDate('closing_date', '>=', today()))->count();

        return $open ?: null;
    }

    protected static function titleExample(): string
    {
        return 'e.g. Pakistan Army Soldier Recruitment 2026';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
