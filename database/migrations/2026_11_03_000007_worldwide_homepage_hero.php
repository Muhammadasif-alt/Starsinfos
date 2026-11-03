<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Homepage hero for worldwide jobs and beauty & style.
 * Each value changes only while it still holds the earlier seeded text, so admin edits are kept.
 */
return new class extends Migration
{
    private const CHANGES = [
        'hero_title' => [
            'Your Trusted Guide to Jobs, Schemes & Style in Pakistan',
            'Worldwide Jobs and Beauty & Style Tips, All in One Place',
        ],
        'hero_highlight' => ['Trusted Guide', 'Worldwide Jobs'],
        'hero_text' => [
            'Latest jobs, government schemes, career advice and beauty and style tips from across Pakistan, explained simply with links to official sources.',
            'Latest jobs in Pakistan and abroad, government schemes, career advice and beauty and style tips, explained simply with links to official sources.',
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
    }
};
