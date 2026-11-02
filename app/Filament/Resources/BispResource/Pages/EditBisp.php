<?php

namespace App\Filament\Resources\BispResource\Pages;

use App\Filament\Resources\BispResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBisp extends EditRecord
{
    protected static string $resource = BispResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')->label('View on site')->icon('heroicon-o-eye')->color('gray')
                ->url(fn () => $this->record->url())->openUrlInNewTab()->visible(fn () => $this->record->status === 'published'),
            Actions\DeleteAction::make(),
        ];
    }
}
