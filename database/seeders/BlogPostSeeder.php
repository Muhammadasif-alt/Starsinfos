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
        ['Government Scheme ke Liye Apply Karne se Pehle 7 Zaroori Baatein', 'Documents, eligibility aur aam ghaltiyan jo application reject karwa deti hain.', 'government-schemes', 'schemes-banner', 'Admin', true],
        ['CV Kaise Banayein: Pakistan Jobs ke Liye Tips', 'Aik saaf aur asar-dar CV banane ke aasan tareeqe.', 'jobs', 'jobs-illustration', 'Sara', false],
        ['Entry Test ki Tayari: 30 Din ka Plan', 'Mahine bhar ka simple study plan aur revision ke tips.', 'education', 'education-banner', 'Ali', false],
        ['Kam Budget mein Ghar ko Naya Look Dein', 'Thoray kharch mein ghar ko behtar banane ke ideas.', 'home-decor', 'decor-warm-living', 'Maria', false],
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
        $paragraph = '<p>Sample content: is article ka asal text admin panel se shamil karein. '.str_repeat('Har qadam par official hidayat parhein, apne documents tayyar rakhein aur waqt par kaam mukammal karein. ', 12).'</p>';

        return "<p>{$excerpt}</p><h2>Shuruaat</h2>{$paragraph}<h2>Aham Baatein</h2>{$paragraph}<h2>Aakhri Mashwara</h2>{$paragraph}";
    }
}
