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
use Illuminate\Database\Eloquent\Builder;

/**
 * All posts in one list. Jobs, Government Schemes, Education and BISP each extend this
 * with their own menu entry, filtered list and a form that only shows the fields they use.
 */
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Site Content';

    protected static ?string $navigationLabel = 'All Posts';

    protected static ?int $navigationSort = 9;

    /** Root category slug this section manages, or null for every post. */
    protected static ?string $rootSlug = null;

    /** Singular word used in labels, e.g. "job". */
    protected static string $noun = 'post';

    public static function getModelLabel(): string
    {
        return static::$noun;
    }

    public static function form(Form $form): Form
    {
        $root = static::$rootSlug;
        $isJob = $root === 'jobs';
        $isScheme = $root === 'government-schemes';
        $all = $root === null;

        return $form->schema([
            Group::make([
                Section::make('Basic details')
                    ->description('The title and summary appear on cards, in search and in Google results.')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        SlugField::title()->label('Title')->placeholder(static::titleExample())->columnSpanFull(),
                        Select::make('category_id')->label('Category')->required()->searchable()->options(static::categoryOptions())
                            ->helperText('Choose the most specific category.'),
                        SlugField::forPost()->label('Web address (slug)')->helperText('Filled in from the title. Change it only if you need to.'),
                        Textarea::make('excerpt')->label('Short summary')->rows(2)->maxLength(300)->columnSpanFull()
                            ->helperText('One or two sentences, around 150 characters.'),
                    ])->columns(2),

                Section::make($isJob ? 'Job details' : ($isScheme ? 'Scheme details' : 'Key details'))
                    ->description($isJob ? 'These power the filters on the Jobs page, so fill them in for every job.' : 'Dates and the official link readers should check.')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextInput::make('department')->label($isJob ? 'Department / organization' : 'Organization')->maxLength(255)
                            ->datalist(fn () => Post::whereNotNull('department')->distinct()->orderBy('department')->pluck('department')->all())
                            ->placeholder('e.g. Pakistan Army')->visible($isJob || $all),
                        TextInput::make('location')->maxLength(255)
                            ->datalist(fn () => Post::whereNotNull('location')->distinct()->orderBy('location')->pluck('location')->all())
                            ->placeholder('e.g. Islamabad, Balochistan, All Pakistan')->visible($isJob || $all),
                        Select::make('job_type')->label('Job type')->options(array_combine(Post::JOB_TYPES, Post::JOB_TYPES))->visible($isJob || $all),
                        DatePicker::make('closing_date')->label($isJob ? 'Last date to apply' : 'Last date (if any)')->native(false)->displayFormat('j M Y')
                            ->helperText('After this date the post shows as Closed.'),
                        Select::make('application_status')->label('Applications')->options(['Open' => 'Open', 'Closed' => 'Closed'])
                            ->helperText('Use when there is no last date.')->visible($isScheme || $all),
                        TextInput::make('source_url')->label('Official website link')->url()->maxLength(255)->columnSpanFull()
                            ->placeholder('https://')->helperText('Readers use this to confirm the details. Always add it.'),
                    ])->columns(2),

                Section::make('Full details')
                    ->description('Eligibility, how to apply, documents and anything else readers need.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        RichEditor::make('body')->hiddenLabel()->required()->fileAttachmentsDisk('public')->fileAttachmentsDirectory('content'),
                    ]),

                SeoSection::make(),
            ])->columnSpan(2),

            Group::make([
                Section::make('Publish')->icon('heroicon-o-paper-airplane')->schema([
                    Select::make('status')->options(['published' => 'Published (live)', 'draft' => 'Draft (hidden)'])->default('published')->required()->native(false),
                    DateTimePicker::make('published_at')->label('Publish date')->default(now())->required()->native(false)->displayFormat('j M Y, g:i a'),
                    Select::make('author_id')->label('Author')->relationship('author', 'name')->required()->default(fn () => auth()->id()),
                ]),
                Section::make('Banner image')->icon('heroicon-o-photo')->schema([
                    FileUpload::make('featured_image')->hiddenLabel()->image()->imageEditor()->disk('public')->directory('posts')->maxSize(4096)
                        ->helperText('Best size 1600 × 900. Under 4 MB.'),
                ]),
                Section::make('Show on homepage')->icon('heroicon-o-star')->schema([
                    Toggle::make('is_featured')->label('Featured')->helperText('The big card in Trending Now. Keep only one on.'),
                    Toggle::make('is_trending')->label('Trending')->helperText('Small cards beside the featured post.'),
                    Toggle::make('is_outdated')->label('Mark as outdated')->helperText('Shows a red Outdated badge on the post.'),
                ]),
            ])->columnSpan(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        $root = static::$rootSlug;
        $isJob = $root === 'jobs';

        return $table
            ->defaultSort('published_at', 'desc')
            ->emptyStateHeading('No '.str(static::$noun)->plural().' yet')
            ->emptyStateDescription('Use the button above to add the first one.')
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')->disk('public')->label('Banner')->width(96)->height(54),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->wrap()->limit(70)
                    ->description(fn (Post $record) => $record->department ?? $record->category?->name),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('closing_date')->label($isJob ? 'Last date' : 'Closes')->date('j M Y')->sortable()
                    ->badge()->color(fn (Post $record) => $record->closing_label === 'Closed' ? 'danger' : 'warning')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),
                Tables\Columns\ToggleColumn::make('is_featured')->label('Featured'),
                Tables\Columns\ToggleColumn::make('is_trending')->label('Trending'),
                Tables\Columns\TextColumn::make('published_at')->label('Published')->date('j M Y')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['published' => 'Published', 'draft' => 'Draft']),
                Tables\Filters\SelectFilter::make('category_id')->label('Category')->options(static::categoryOptions()),
                Tables\Filters\Filter::make('open')->label('Still open')
                    ->query(fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNull('closing_date')->orWhereDate('closing_date', '>=', today()))),
                Tables\Filters\Filter::make('closed')->label('Closed')->query(fn (Builder $query) => $query->whereDate('closing_date', '<', today())),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('View')->icon('heroicon-o-eye')->color('gray')
                    ->url(fn (Post $record) => $record->url())->openUrlInNewTab()->visible(fn (Post $record) => $record->status === 'published'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('publish')->label('Publish')->icon('heroicon-o-check-circle')
                    ->action(fn ($records) => $records->each->update(['status' => 'published'])),
                Tables\Actions\BulkAction::make('draft')->label('Move to draft')->icon('heroicon-o-eye-slash')
                    ->action(fn ($records) => $records->each->update(['status' => 'draft'])),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('category');

        return static::$rootSlug ? $query->whereIn('category_id', static::familyIds()) : $query;
    }

    protected static function familyIds(): array
    {
        return Category::where('slug', static::$rootSlug)->first()?->familyIds() ?? [];
    }

    protected static function titleExample(): string
    {
        return 'e.g. Pakistan Army Soldier Recruitment 2026';
    }

    /** Categories for selects: this section's subcategories, or every category grouped by section. */
    protected static function categoryOptions(): array
    {
        if (static::$rootSlug) {
            $root = Category::with('children')->where('slug', static::$rootSlug)->first();

            return $root ? [$root->id => $root->name.' (general)'] + $root->children->pluck('name', 'id')->all() : [];
        }

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
