<?php

namespace App\Filament\Resources\BeautyResource\Pages;

use App\Filament\Resources\BeautyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBeautyPosts extends ListRecords
{
    protected static string $resource = BeautyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Add beauty post')->icon('heroicon-o-plus')];
    }
}
