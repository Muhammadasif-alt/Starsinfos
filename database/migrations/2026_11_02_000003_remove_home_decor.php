<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Removes the Home & Decor category with its posts, and its mentions in seeded copy.
 * Page text and the stat change only if they still hold the seeded wording.
 */
return new class extends Migration
{
    private const PAGE_TEXT = [
        'admissions, news and home decor in one place' => 'admissions and news in one place',
        'jobs, government schemes, education and home decor for people' => 'jobs, government schemes and education for people',
        'jobs, education, careers or home decor' => 'jobs, education or careers',
        'jobs, education, careers and home decor' => 'jobs, education and careers',
    ];

    public function up(): void
    {
        $root = DB::table('categories')->where('slug', 'home-decor')->value('id');

        if ($root) {
            $family = DB::table('categories')->where('parent_id', $root)->pluck('id')->push($root);

            DB::table('posts')->whereIn('category_id', $family)->delete();
            DB::table('blog_posts')->whereIn('category_id', $family)->delete();
            DB::table('categories')->where('parent_id', $root)->delete();
            DB::table('categories')->where('id', $root)->delete();
        }

        foreach (DB::table('pages')->get(['id', 'body', 'excerpt']) as $page) {
            DB::table('pages')->where('id', $page->id)->update([
                'body' => strtr($page->body, self::PAGE_TEXT),
                'excerpt' => $page->excerpt === null ? null : strtr($page->excerpt, self::PAGE_TEXT),
            ]);
        }

        $stats = json_decode((string) DB::table('settings')->where('key', 'stats')->value('value'), true);

        if (($stats[0]['value'] ?? null) === '6' && ($stats[0]['label'] ?? null) === 'Main Categories') {
            $stats[0]['value'] = '5';
            DB::table('settings')->where('key', 'stats')->update(['value' => json_encode($stats)]);
        }
    }
};
