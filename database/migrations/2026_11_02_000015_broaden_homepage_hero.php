<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Presents the homepage as a guide to all of Pakistan's public information, not jobs only.
 * Each value changes only while it still holds the earlier seeded text, so admin edits are kept.
 */
return new class extends Migration
{
    private const CHANGES = [
        'hero_title' => [
            'Find Your Next Job in Pakistan',
            'Your Trusted Guide to Jobs, Schemes & Education in Pakistan',
        ],
        'hero_text' => [
            'Government, private, bank and teaching jobs with last dates and official sources, plus schemes and admissions in one place.',
            'Latest jobs, government schemes, BISP 8171, admissions and career advice from across Pakistan, explained simply with links to official sources.',
        ],
        'hero_chips' => [
            [
                ['label' => 'Government Jobs', 'url' => '/jobs/government-jobs'],
                ['label' => 'Bank Jobs', 'url' => '/jobs/bank-jobs'],
                ['label' => 'Teaching Jobs', 'url' => '/jobs/teaching-jobs'],
                ['label' => 'Work From Home', 'url' => '/jobs/online-work-from-home-jobs'],
            ],
            [
                ['label' => 'Latest Jobs', 'url' => '/jobs'],
                ['label' => 'Government Schemes', 'url' => '/government-schemes'],
                ['label' => 'BISP 8171', 'url' => '/bisp-8171'],
                ['label' => 'Admissions', 'url' => '/education'],
                ['label' => 'Career Advice', 'url' => '/blog'],
            ],
        ],
    ];

    public function up(): void
    {
        foreach (self::CHANGES as $key => [$old, $new]) {
            $current = DB::table('settings')->where('key', $key)->value('value');

            if ($current !== null && json_decode($current, true) === $old) {
                DB::table('settings')->where('key', $key)->update(['value' => json_encode($new)]);
            }
        }

        DB::table('settings')->insertOrIgnore(['key' => 'hero_highlight', 'value' => json_encode('Trusted Guide'), 'created_at' => now(), 'updated_at' => now()]);

        DB::table('posts')
            ->where('slug', 'lower-division-clerk-bps-09-drug-regulatory-authority-balochistan')
            ->whereNull('featured_image')
            ->update(['featured_image' => 'posts/dra-lower-division-clerk.jpg']);
    }
};
