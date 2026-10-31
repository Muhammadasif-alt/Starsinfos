<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Support\SlugField;
use App\Models\Category;
use App\Models\Page;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('parent_id')
                ->label('Parent category')
                ->relationship('parent', 'name', fn (Builder $query) => $query->whereNull('parent_id'))
                ->helperText('Leave empty for a main category.'),
            SlugField::title('name'),
            SlugField::topLevel(Page::class),
            ColorPicker::make('color')->required()->default('#0B6B4F'),
            TextInput::make('icon')->maxLength(16)->helperText('An emoji, e.g. 💼'),
            TextInput::make('sort_order')->numeric()->default(0),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            FileUpload::make('image')->image()->disk('public')->directory('categories')->maxSize(4096)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('public'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('parent.name')->placeholder('Main category')->sortable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\TextColumn::make('posts_count')->counts('posts')->label('Posts'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('parent_id')->label('Type')->nullable()
                    ->trueLabel('Subcategories')->falseLabel('Main categories'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
