<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Points the homepage hero at jobs and gives job subcategories icons.
 * Values change only if they still hold the seeded text, so admin edits are kept.
 */
return new class extends Migration
{
    private const SETTINGS = [
        'hero_title' => [
            'Reliable Information on Jobs, Schemes, BISP and Education',
            'Find Your Next Job in Pakistan',
        ],
        'hero_text' => [
            'Latest jobs, government schemes, BISP 8171 updates, admissions and home decor ideas, all in one place.',
            'Government, private, bank and teaching jobs with last dates and official sources, plus schemes and admissions in one place.',
        ],
        'hero_chips' => [
            [
                ['label' => 'Latest Jobs in Pakistan', 'url' => '/jobs/latest-jobs-in-pakistan'],
                ['label' => '8171 Eligibility', 'url' => '/bisp-8171/8171-eligibility-guide'],
                ['label' => 'Entry Tests', 'url' => '/education/entry-tests'],
                ['label' => 'Solar Schemes', 'url' => '/government-schemes/solar-schemes'],
            ],
            [
                ['label' => 'Government Jobs', 'url' => '/jobs/government-jobs'],
                ['label' => 'Bank Jobs', 'url' => '/jobs/bank-jobs'],
                ['label' => 'Teaching Jobs', 'url' => '/jobs/teaching-jobs'],
                ['label' => 'Work From Home', 'url' => '/jobs/online-work-from-home-jobs'],
            ],
        ],
    ];

    private const ICONS = [
        'government-jobs' => '🏛️',
        'private-jobs' => '🏢',
        'bank-jobs' => '🏦',
        'army-police-jobs' => '🛡️',
        'teaching-jobs' => '📚',
        'wapda-railway-jobs' => '⚡',
        'fpsc-ppsc-jobs' => '📝',
        'online-work-from-home-jobs' => '💻',
        'latest-jobs-in-pakistan' => '🔔',
    ];

    public function up(): void
    {
        foreach (self::SETTINGS as $key => [$old, $new]) {
            $current = DB::table('settings')->where('key', $key)->value('value');

            if ($current !== null && json_decode($current, true) === $old) {
                DB::table('settings')->where('key', $key)->update(['value' => json_encode($new)]);
            }
        }

        foreach (self::ICONS as $slug => $icon) {
            DB::table('categories')->where('slug', $slug)->whereNull('icon')->update(['icon' => $icon]);
        }
    }
};
