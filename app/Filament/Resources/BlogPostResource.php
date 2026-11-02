<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Filament\Support\SeoSection;
use App\Filament\Support\SlugField;
use App\Models\BlogPost;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Group::make([
                Section::make([
                    SlugField::title(),
                    SlugField::make(),
                    Textarea::make('excerpt')->rows(2),
                    RichEditor::make('body')->required()->fileAttachmentsDisk('public')->fileAttachmentsDirectory('content'),
                ]),
                SeoSection::make(),
            ])->columnSpan(2),
            Group::make([
                Section::make('Publish')->schema([
                    Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required(),
                    DateTimePicker::make('published_at')->default(now())->required(),
                    Select::make('topic')->label('Career topic')->options(collect(BlogPost::TOPICS)->map(fn (array $topic) => $topic['label'])->all()),
                    Select::make('category_id')->label('Category')
                        ->relationship('category', 'name', fn (Builder $query) => $query->whereNull('parent_id')),
                    Select::make('author_id')->label('Author')->relationship('author', 'name')->required()->default(fn () => auth()->id()),
                    Toggle::make('is_featured')->label('Featured'),
                ]),
                Section::make('Image')->schema([
                    FileUpload::make('featured_image')->hiddenLabel()->image()->disk('public')->directory('blog')->maxSize(4096),
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
                Tables\Columns\TextColumn::make('author.name')->sortable(),
                Tables\Columns\TextColumn::make('category.name')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('read_time')->suffix(' min'),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('published_at')->dateTime('j M Y')->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
