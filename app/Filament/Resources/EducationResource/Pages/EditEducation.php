<?php

namespace App\Filament\Resources\EducationResource\Pages;

use App\Filament\Resources\EducationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEducation extends EditRecord
{
    protected static string $resource = EducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')->label('View on site')->icon('heroicon-o-eye')->color('gray')
                ->url(fn () => $this->record->url())->openUrlInNewTab()->visible(fn () => $this->record->status === 'published'),
            Actions\DeleteAction::make(),
        ];
    }
}
