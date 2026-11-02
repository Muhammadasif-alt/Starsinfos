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

    protected static ?string $navigationGroup = 'Publish';

    protected static ?string $navigationLabel = 'Career Advice';

    protected static ?string $modelLabel = 'career article';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Group::make([
                Section::make('Article')->description('Write in simple, friendly language. Read time is worked out automatically.')->icon('heroicon-o-pencil')->schema([
                    SlugField::title(),
                    SlugField::make(),
                    Textarea::make('excerpt')->label('Short summary')->rows(2)->maxLength(300)->helperText('One or two sentences shown on cards and in Google results.'),
                    RichEditor::make('body')->required()->fileAttachmentsDisk('public')->fileAttachmentsDirectory('content'),
                ]),
                SeoSection::make(),
            ])->columnSpan(2),
            Group::make([
                Section::make('Publish')->icon('heroicon-o-paper-airplane')->schema([
                    Select::make('status')->options(['published' => 'Published (live)', 'draft' => 'Draft (hidden)'])->default('published')->required()->native(false),
                    DateTimePicker::make('published_at')->default(now())->required(),
                    Select::make('topic')->label('Career topic')->required()->helperText('Decides where the article appears on the Career Advice page.')->options(collect(BlogPost::TOPICS)->map(fn (array $topic) => $topic['label'])->all()),
                    Select::make('category_id')->label('Category')
                        ->relationship('category', 'name', fn (Builder $query) => $query->whereNull('parent_id')),
                    Select::make('author_id')->label('Author')->relationship('author', 'name')->required()->default(fn () => auth()->id()),
                    Toggle::make('is_featured')->label('Featured'),
                ]),
                Section::make('Banner image')->icon('heroicon-o-photo')->schema([
                    FileUpload::make('featured_image')->hiddenLabel()->image()->imageEditor()->disk('public')->directory('blog')->maxSize(4096)->helperText('Best size 1600 × 900. Under 4 MB.'),
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
