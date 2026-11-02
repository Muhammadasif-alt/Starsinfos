<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Removes the News category with its posts, and its mentions in seeded copy.
 * Text and the stat change only if they still hold the seeded wording.
 */
return new class extends Migration
{
    private const TEXT = [
        'BISP 8171, admissions and news in one place' => 'BISP 8171 and admissions in one place',
        'government schemes, admissions and news to help' => 'government schemes and admissions to help',
    ];

    public function up(): void
    {
        $root = DB::table('categories')->where('slug', 'news')->value('id');

        if ($root) {
            $family = DB::table('categories')->where('parent_id', $root)->pluck('id')->push($root);

            DB::table('posts')->whereIn('category_id', $family)->delete();
            DB::table('blog_posts')->whereIn('category_id', $family)->update(['category_id' => null]);
            DB::table('categories')->where('parent_id', $root)->delete();
            DB::table('categories')->where('id', $root)->delete();
        }

        foreach (DB::table('pages')->get(['id', 'body']) as $page) {
            DB::table('pages')->where('id', $page->id)->update(['body' => strtr($page->body, self::TEXT)]);
        }

        $footer = DB::table('settings')->where('key', 'footer_text')->value('value');

        if ($footer !== null) {
            DB::table('settings')->where('key', 'footer_text')->update(['value' => json_encode(strtr(json_decode($footer), self::TEXT))]);
        }

        $stats = json_decode((string) DB::table('settings')->where('key', 'stats')->value('value'), true);

        if (($stats[0]['value'] ?? null) === '5' && ($stats[0]['label'] ?? null) === 'Main Categories') {
            $stats[0]['value'] = '4';
            DB::table('settings')->where('key', 'stats')->update(['value' => json_encode($stats)]);
        }
    }
};
