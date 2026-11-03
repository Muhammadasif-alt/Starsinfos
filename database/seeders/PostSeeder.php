<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\Concerns\SeedsImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    use SeedsImages;

    /** Keyed by source URL: [subcategory slug, title, image, closes in days or null, flags] */
    private const POSTS = [
        'https://www.fpsc.gov.pk' => [
            ['government-jobs', 'Government Jobs – Clerk & Assistant Posts', 'jobs-banner', 15, []],
            ['bank-jobs', 'Bank Jobs – Officer Grade Hiring', 'jobs-blocks', 20, ['is_trending']],
            ['army-police-jobs', 'Army & Police – Constable Recruitment', 'jobs-illustration', 25, []],
            ['teaching-jobs', 'Teaching Jobs – FPSC & PPSC Updates', 'jobs-blocks', 30, ['is_trending']],
            ['online-work-from-home-jobs', 'Work From Home – Online Jobs', 'jobs-illustration', null, []],
            ['private-jobs', 'Healthcare Jobs in Private Hospitals', 'jobs-healthcare', 35, []],
        ],
        'https://www.pakistan.gov.pk' => [
            ['solar-schemes', 'Solar Scheme Registration Guide', 'scheme-solar', null, ['is_trending']],
            ['youth-loan-schemes', 'Youth Loan Scheme – Eligibility & How to Apply', 'schemes-banner', null, []],
            ['student-scholarships', 'Free Laptop Scheme for Students – Complete Guide', 'scheme-laptops', null, []],
            ['business-support-programs', 'Green Tractor Program – Farmers Guide', 'scheme-tractor', null, []],
            ['housing-schemes', 'Housing Schemes – Application Process', 'schemes-banner', null, []],
            ['eligibility-application-process', 'Government Scheme Eligibility – Common Requirements', 'schemes-banner', null, []],
        ],
    ];

    public function run(): void
    {
        $authors = collect(['Admin', 'Sara'])->map(fn (string $name) => User::firstOrCreate(
            ['email' => Str::slug($name).'.author@example.com'],
            ['name' => $name, 'password' => Hash::make(Str::random(40)), 'role' => 'author', 'bio' => 'Editorial contributor.'],
        ));

        $index = 0;

        foreach (self::POSTS as $source => $posts) {
            foreach ($posts as [$categorySlug, $title, $image, $closesIn, $flags]) {
                Post::updateOrCreate(['slug' => Str::slug($title)], [
                    'category_id' => Category::where('slug', $categorySlug)->value('id'),
                    'author_id' => $authors[$index % 2]->id,
                    'title' => $title,
                    'excerpt' => "A guide to {$title}: key details, requirements and next steps.",
                    'body' => $this->body($title),
                    'featured_image' => $this->seedImage($image),
                    'source_url' => $source ?: null,
                    'status' => 'published',
                    'is_featured' => in_array('is_featured', $flags, true),
                    'is_trending' => in_array('is_trending', $flags, true),
                    'closing_date' => $closesIn ? now()->addDays($closesIn) : null,
                    'published_at' => now()->subHours(($index * 7 + 1) % 200),
                ]);

                $index++;
            }
        }
    }

    private function body(string $title): string
    {
        return <<<HTML
            <p><strong>Sample content:</strong> this is demo text. Replace it with information verified from the official source before publishing.</p>
            <p>This guide gives general information about {$title}.</p>
            <h2>Eligibility</h2>
            <ul><li>Read the requirements in the official notification.</li><li>Keep the required documents ready in advance.</li></ul>
            <h2>How to Apply</h2>
            <ol><li>Open the official website.</li><li>Fill in the form carefully.</li><li>Submit it before the last date.</li></ol>
            HTML;
    }
}
