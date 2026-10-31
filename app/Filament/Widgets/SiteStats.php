<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Published posts', Post::published()->count()),
            Stat::make('Blog posts', BlogPost::published()->count()),
            Stat::make('Subscribers', Subscriber::count()),
            Stat::make('Messages', ContactMessage::count()),
        ];
    }
}
