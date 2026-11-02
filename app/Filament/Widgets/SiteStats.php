<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\JobResource;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $jobIds = Category::where('slug', 'jobs')->first()?->familyIds() ?? [];
        $jobs = fn () => Post::published()->whereIn('category_id', $jobIds);

        return [
            Stat::make('Open jobs', $jobs()->where(fn ($q) => $q->whereNull('closing_date')->orWhereDate('closing_date', '>=', today()))->count())
                ->description('Still accepting applications')->color('success')->url(JobResource::getUrl('index')),
            Stat::make('Closing in 7 days', $jobs()->whereBetween('closing_date', [today(), today()->addDays(7)])->count())
                ->description('Check these are still correct')->color('warning'),
            Stat::make('Drafts', Post::where('status', 'draft')->count())->description('Not yet live'),
            Stat::make('Messages', ContactMessage::count())->description('From the contact form'),
        ];
    }
}
