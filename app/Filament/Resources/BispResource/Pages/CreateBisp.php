<?php

namespace App\Filament\Resources\BispResource\Pages;

use App\Filament\Resources\BispResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBisp extends CreateRecord
{
    protected static string $resource = BispResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
