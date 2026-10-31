<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Support\SeoSection;
use App\Filament\Support\SlugField;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Group::make([
                Section::make([
                    SlugField::title(),
                    SlugField::forPost(),
                    Textarea::make('excerpt')->rows(2),
                    RichEditor::make('body')->required()->fileAttachmentsDisk('public')->fileAttachmentsDirectory('content'),
                    TextInput::make('source_url')->label('Official source URL')->url()->maxLength(255),
                ]),
                SeoSection::make(),
            ])->columnSpan(2),
            Group::make([
                Section::make('Publish')->schema([
                    Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required(),
                    DateTimePicker::make('published_at')->default(now())->required(),
                    Select::make('category_id')->label('Category')->required()->searchable()->options(self::categoryOptions()),
                    Select::make('author_id')->label('Author')->relationship('author', 'name')->required()->default(fn () => auth()->id()),
                    DatePicker::make('closing_date')->helperText('For jobs and admissions.'),
                ]),
                Section::make('Flags')->schema([
                    Toggle::make('is_featured')->label('Featured'),
                    Toggle::make('is_trending')->label('Trending'),
                    Toggle::make('is_outdated')->label('Outdated'),
                ]),
                Section::make('Image')->schema([
                    FileUpload::make('featured_image')->hiddenLabel()->image()->disk('public')->directory('posts')->maxSize(4096),
                ]),
            ])->columnSpan(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')->disk('public')->label('Image'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50)->sortable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\IconColumn::make('is_trending')->boolean()->label('Trending'),
                Tables\Columns\IconColumn::make('is_outdated')->boolean()->label('Outdated'),
                Tables\Columns\TextColumn::make('closing_date')->date('j M Y')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime('j M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published']),
                Tables\Filters\SelectFilter::make('category_id')->label('Category')->options(self::categoryOptions()),
                Tables\Filters\TernaryFilter::make('is_outdated')->label('Outdated'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('category');
    }

    /** Subcategories grouped under their main category, for select inputs. */
    private static function categoryOptions(): array
    {
        return Category::roots()->with('children')->get()->mapWithKeys(fn (Category $root) => [
            $root->name => [$root->id => $root->name.' (main)'] + $root->children->pluck('name', 'id')->all(),
        ])->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
