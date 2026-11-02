<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EducationResource\Pages;
use App\Models\Post;

class EducationResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Publish';

    protected static ?string $navigationLabel = 'Education & Admissions';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'education-posts';

    protected static ?string $rootSlug = 'education';

    protected static string $noun = 'education update';

    protected static function titleExample(): string
    {
        return 'e.g. BS Admissions 2026 Open at Punjab University';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEducations::route('/'),
            'create' => Pages\CreateEducation::route('/create'),
            'edit' => Pages\EditEducation::route('/{record}/edit'),
        ];
    }
}
