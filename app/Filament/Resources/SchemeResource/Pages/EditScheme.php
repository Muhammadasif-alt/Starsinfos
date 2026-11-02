<?php

namespace App\Filament\Resources\SchemeResource\Pages;

use App\Filament\Resources\SchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheme extends EditRecord
{
    protected static string $resource = SchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')->label('View on site')->icon('heroicon-o-eye')->color('gray')
                ->url(fn () => $this->record->url())->openUrlInNewTab()->visible(fn () => $this->record->status === 'published'),
            Actions\DeleteAction::make(),
        ];
    }
}
