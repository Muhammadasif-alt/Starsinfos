<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\Concerns\SeedsImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    use SeedsImages;

    private const POSTS = [
        ['7 Things to Know Before Applying for a Government Scheme', 'Documents, eligibility and the common mistakes that get applications rejected.', 'government-schemes', 'schemes-banner', 'Admin', true],
        ['How to Write a CV: Tips for Jobs in Pakistan', 'Simple ways to build a clear and effective CV.', 'jobs', 'jobs-illustration', 'Sara', false],
        ['Entry Test Preparation: A 30-Day Plan', 'A simple month-long study plan with revision tips.', 'education', 'education-banner', 'Ali', false],
        ['Give Your Home a New Look on a Small Budget', 'Affordable ideas to refresh your home.', 'home-decor', 'decor-warm-living', 'Maria', false],
    ];

    public function run(): void
    {
        foreach (self::POSTS as $index => [$title, $excerpt, $categorySlug, $image, $authorName, $featured]) {
            $author = User::firstOrCreate(
                ['email' => Str::slug($authorName).'.author@example.com'],
                ['name' => $authorName, 'password' => Hash::make(Str::random(40)), 'role' => 'author', 'bio' => 'Editorial contributor.'],
            );

            BlogPost::updateOrCreate(['slug' => Str::slug($title)], [
                'author_id' => $author->id,
                'category_id' => Category::where('slug', $categorySlug)->value('id'),
                'title' => $title,
                'excerpt' => $excerpt,
                'body' => $this->body($excerpt),
                'featured_image' => $this->seedImage($image),
                'status' => 'published',
                'is_featured' => $featured,
                'published_at' => now()->subDays($index),
            ]);
        }
    }

    private function body(string $excerpt): string
    {
        $paragraph = '<p>Sample content: add the final text of this article from the admin panel. '.str_repeat('Read the official instructions at every step, keep your documents ready and finish each task on time. ', 12).'</p>';

        return "<p>{$excerpt}</p><h2>Getting Started</h2>{$paragraph}<h2>Key Points</h2>{$paragraph}<h2>Final Advice</h2>{$paragraph}";
    }
}
