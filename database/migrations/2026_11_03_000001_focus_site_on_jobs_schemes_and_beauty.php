<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Narrows the site to Jobs, Government Schemes, Beauty & Style and Career Advice:
 * removes BISP 8171, Education & Admissions, the admissions calendar and Write for Us,
 * strips links to them from stored content, and adds the Beauty & Style category.
 * Seeded text changes only where the original wording is still in place.
 */
return new class extends Migration
{
    private const REMOVED_ROOTS = ['bisp-8171', 'education'];

    private const BEAUTY = [
        ['Skincare', 'droplet'],
        ['Makeup', 'sparkles'],
        ['Hair Care', 'scissors'],
        ['Fashion & Clothing', 'shirt'],
        ['Bridal', 'heart'],
        ["Men's Grooming", 'user'],
    ];

    private const TEXT = [
        'BISP 8171 and admissions in one place' => 'beauty and style tips in one place',
        'about jobs, government schemes and admissions' => 'about jobs and government schemes',
        'jobs, government schemes and education for people' => 'jobs, government schemes, careers and style for people',
    ];

    private const SETTINGS = [
        'tagline' => [
            'Trusted information on jobs, government schemes and education in Pakistan.',
            'Trusted information on jobs, government schemes, careers and style in Pakistan.',
        ],
        'hero_title' => [
            'Your Trusted Guide to Jobs, Schemes & Education in Pakistan',
            'Your Trusted Guide to Jobs, Schemes & Style in Pakistan',
        ],
        'hero_text' => [
            'Latest jobs, government schemes, BISP 8171, admissions and career advice from across Pakistan, explained simply with links to official sources.',
            'Latest jobs, government schemes, career advice and beauty and style tips from across Pakistan, explained simply with links to official sources.',
        ],
        'footer_text' => [
            'Find government, private, bank and teaching jobs in Pakistan, with last dates and links to official sources. We also share career advice, government schemes and admissions to help you take your next step. This is an information website, not an official government portal, so always confirm details with the official source.',
            'Find government, private, bank and teaching jobs in Pakistan, with last dates and links to official sources. We also share government schemes, career advice and beauty and style tips. This is an information website, not an official government portal, so always confirm details with the official source.',
        ],
        'hero_chips' => [
            [
                ['label' => 'Latest Jobs', 'url' => '/jobs'],
                ['label' => 'Government Schemes', 'url' => '/government-schemes'],
                ['label' => 'BISP 8171', 'url' => '/bisp-8171'],
                ['label' => 'Admissions', 'url' => '/education'],
                ['label' => 'Career Advice', 'url' => '/blog'],
            ],
            [
                ['label' => 'Latest Jobs', 'url' => '/jobs'],
                ['label' => 'Government Schemes', 'url' => '/government-schemes'],
                ['label' => 'Beauty & Style', 'url' => '/beauty-style'],
                ['label' => 'Career Advice', 'url' => '/blog'],
            ],
        ],
    ];

    public function up(): void
    {
        $this->removeSections();
        $this->stripLinks();
        $this->updateCopy();
        $this->addBeauty();

        Schema::dropIfExists('admissions');
    }

    private function removeSections(): void
    {
        $jobs = DB::table('categories')->where('slug', 'jobs')->value('id');

        foreach (self::REMOVED_ROOTS as $slug) {
            $root = DB::table('categories')->where('slug', $slug)->value('id');

            if (! $root) {
                continue;
            }

            $family = DB::table('categories')->where('parent_id', $root)->pluck('id')->push($root);

            DB::table('posts')->whereIn('category_id', $family)->delete();
            DB::table('blog_posts')->whereIn('category_id', $family)->update(['category_id' => $jobs]);
            DB::table('categories')->where('parent_id', $root)->delete();
            DB::table('categories')->where('id', $root)->delete();
        }

        DB::table('pages')->where('slug', 'write-for-us')->delete();
        DB::table('faqs')->where('question', 'How up to date is the BISP 8171 information?')->delete();
        DB::table('faqs')->where('answer', 'Yes. Please get in touch through our "Write for Us" page.')
            ->update(['answer' => 'Yes. Send us your topic and a writing sample through our Contact Us page.']);
    }

    /** Removes list items that link to the deleted sections from post, blog and page bodies. */
    private function stripLinks(): void
    {
        $pattern = '#<li><a href="/(?:education|bisp-8171|write-for-us)[^"]*">[^<]*</a></li>#';

        foreach (['posts', 'blog_posts', 'pages'] as $table) {
            foreach (DB::table($table)->where('body', 'like', '%href="/education%')
                ->orWhere('body', 'like', '%href="/bisp-8171%')
                ->orWhere('body', 'like', '%href="/write-for-us%')
                ->get(['id', 'body']) as $row) {
                $body = preg_replace($pattern, '', $row->body);
                $body = preg_replace('#<a href="/(?:education|bisp-8171|write-for-us)[^"]*">([^<]*)</a>#', '$1', $body);
                DB::table($table)->where('id', $row->id)->update(['body' => $body]);
            }
        }
    }

    private function updateCopy(): void
    {
        foreach (DB::table('pages')->get(['id', 'body', 'excerpt']) as $page) {
            DB::table('pages')->where('id', $page->id)->update(['body' => strtr($page->body, self::TEXT), 'excerpt' => $page->excerpt === null ? null : strtr($page->excerpt, self::TEXT)]);
        }

        foreach (self::SETTINGS as $key => [$old, $new]) {
            $current = DB::table('settings')->where('key', $key)->value('value');

            if ($current !== null && json_decode($current, true) === $old) {
                DB::table('settings')->where('key', $key)->update(['value' => json_encode($new)]);
            }
        }

        $stats = json_decode((string) DB::table('settings')->where('key', 'stats')->value('value'), true);

        if (($stats[0]['value'] ?? null) === '4' && ($stats[0]['label'] ?? null) === 'Main Categories') {
            $stats[0]['value'] = '3';
            DB::table('settings')->where('key', 'stats')->update(['value' => json_encode($stats)]);
        }
    }

    private function addBeauty(): void
    {
        if (DB::table('categories')->where('slug', 'beauty-style')->exists()) {
            return;
        }

        $now = now();
        $root = DB::table('categories')->insertGetId([
            'name' => 'Beauty & Style',
            'slug' => 'beauty-style',
            'description' => 'Skincare, makeup, hair care, fashion and grooming tips',
            'color' => '#B83280',
            'icon' => 'sparkles',
            'sort_order' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach (self::BEAUTY as $order => [$name, $icon]) {
            DB::table('categories')->insert([
                'parent_id' => $root,
                'name' => $name,
                'slug' => Str::slug($name),
                'color' => '#B83280',
                'icon' => $icon,
                'sort_order' => $order,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
