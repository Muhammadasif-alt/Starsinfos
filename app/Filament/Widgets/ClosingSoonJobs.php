<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\JobResource;
use App\Models\Category;
use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ClosingSoonJobs extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Jobs closing soon';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::published()
                ->whereIn('category_id', Category::where('slug', 'jobs')->first()?->familyIds() ?? [])
                ->whereDate('closing_date', '>=', today())
                ->orderBy('closing_date'))
            ->columns([
                Tables\Columns\TextColumn::make('title')->wrap(),
                Tables\Columns\TextColumn::make('department')->placeholder('—'),
                Tables\Columns\TextColumn::make('closing_date')->label('Last date')->date('j M Y')->badge()->color('warning'),
            ])
            ->recordUrl(fn (Post $record) => JobResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('No upcoming deadlines')
            ->paginated([5]);
    }
}
