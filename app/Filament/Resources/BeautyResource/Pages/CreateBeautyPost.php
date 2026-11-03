<?php

namespace App\Filament\Resources\BeautyResource\Pages;

use App\Filament\Resources\BeautyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBeautyPost extends CreateRecord
{
    protected static string $resource = BeautyResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
