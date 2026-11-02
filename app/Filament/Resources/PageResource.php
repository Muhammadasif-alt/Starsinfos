<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\SlugField;
use App\Models\Category;
use App\Models\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            SlugField::title(),
            SlugField::topLevel(Category::class),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('published')->required(),
            Textarea::make('excerpt')->label('Hero text')->rows(2)->maxLength(500)->columnSpanFull(),
            RichEditor::make('body')->required()->columnSpanFull(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('updated_at')->since(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
