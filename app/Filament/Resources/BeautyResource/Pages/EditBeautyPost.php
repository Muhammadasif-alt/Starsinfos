<?php

namespace App\Filament\Resources\BeautyResource\Pages;

use App\Filament\Resources\BeautyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBeautyPost extends EditRecord
{
    protected static string $resource = BeautyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')->label('View on site')->icon('heroicon-o-eye')->color('gray')
                ->url(fn () => $this->record->url())->openUrlInNewTab()->visible(fn () => $this->record->status === 'published'),
            Actions\DeleteAction::make(),
        ];
    }
}
