<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BispResource;
use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\EducationResource;
use App\Filament\Resources\JobResource;
use App\Filament\Resources\SchemeResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return ['actions' => [
            ['label' => 'Add Job', 'hint' => 'New vacancy with last date', 'icon' => 'heroicon-o-briefcase', 'url' => JobResource::getUrl('create')],
            ['label' => 'Add Government Scheme', 'hint' => 'Loan, housing, solar…', 'icon' => 'heroicon-o-building-library', 'url' => SchemeResource::getUrl('create')],
            ['label' => 'Add Education Update', 'hint' => 'Admissions, results, tests', 'icon' => 'heroicon-o-academic-cap', 'url' => EducationResource::getUrl('create')],
            ['label' => 'Add Career Article', 'hint' => 'CV, interview, skills', 'icon' => 'heroicon-o-pencil-square', 'url' => BlogPostResource::getUrl('create')],
            ['label' => 'Add BISP Update', 'hint' => 'Payments, eligibility', 'icon' => 'heroicon-o-credit-card', 'url' => BispResource::getUrl('create')],
        ]];
    }
}
