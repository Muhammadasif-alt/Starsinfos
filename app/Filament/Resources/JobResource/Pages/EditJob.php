<?php

namespace App\Filament\Resources\JobResource\Pages;

use App\Filament\Resources\JobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJob extends EditRecord
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')->label('View on site')->icon('heroicon-o-eye')->color('gray')
                ->url(fn () => $this->record->url())->openUrlInNewTab()->visible(fn () => $this->record->status === 'published'),
            Actions\DeleteAction::make(),
        ];
    }
}
