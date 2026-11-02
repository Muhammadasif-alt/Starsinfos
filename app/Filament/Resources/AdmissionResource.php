<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdmissionResource\Pages;
use App\Models\Admission;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdmissionResource extends Resource
{
    protected static ?string $model = Admission::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Site Content';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('program')->required()->maxLength(255),
            TextInput::make('type')->required()->maxLength(255)->datalist(['University', 'College', 'Entry Test']),
            DatePicker::make('last_date')->required(),
            DatePicker::make('test_date')->label('Entry test date')->helperText('Leave empty if there is no test.'),
            TextInput::make('link')->url()->maxLength(255),
            TextInput::make('sort_order')->numeric()->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('program')->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('last_date')->date('j M Y')->sortable(),
                Tables\Columns\TextColumn::make('test_date')->date('j M Y')->placeholder('No test'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageAdmissions::route('/')];
    }
}
