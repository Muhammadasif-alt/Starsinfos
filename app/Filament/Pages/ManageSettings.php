<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * @property Form $form
 */
class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Site';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Site Settings';

    protected static ?string $slug = 'settings';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::query()->pluck('value', 'key')->all());
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Section::make('General')->schema([
                TextInput::make('tagline')->required()->maxLength(255),
                Textarea::make('disclaimer')->required()->rows(2),
                Textarea::make('footer_text')->label('Footer description')->required()->rows(3),
            ]),
            Section::make('Hero')->schema([
                TextInput::make('hero_title')->required()->maxLength(255),
                Textarea::make('hero_text')->required()->rows(2),
                Repeater::make('hero_chips')->label('Quick chips')->columns(2)->maxItems(6)->schema([
                    TextInput::make('label')->required(),
                    TextInput::make('url')->required()->helperText('e.g. /jobs/bank-jobs'),
                ]),
            ]),
            Section::make('Stats strip')->schema([
                Repeater::make('stats')->hiddenLabel()->columns(2)->minItems(4)->maxItems(4)->reorderable(false)->schema([
                    TextInput::make('value')->required(),
                    TextInput::make('label')->required(),
                ]),
            ]),
            Section::make('How to Apply')->schema([
                Repeater::make('steps')->hiddenLabel()->columns(2)->minItems(4)->maxItems(4)->schema([
                    TextInput::make('title')->required(),
                    TextInput::make('text')->required(),
                ]),
            ]),
            Section::make('Social links')->schema([
                Repeater::make('social_links')->hiddenLabel()->columns(2)->defaultItems(0)->schema([
                    TextInput::make('label')->required()->helperText('e.g. Facebook'),
                    TextInput::make('url')->required()->url(),
                ]),
            ]),
        ]);
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Notification::make()->title('Settings saved')->success()->send();
    }
}
