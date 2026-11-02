<?php

namespace App\Filament\Resources\BispResource\Pages;

use App\Filament\Resources\BispResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBisps extends ListRecords
{
    protected static string $resource = BispResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Add BISP update')->icon('heroicon-o-plus')];
    }
}
