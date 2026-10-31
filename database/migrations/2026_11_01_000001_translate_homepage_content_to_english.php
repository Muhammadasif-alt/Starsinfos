<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Moves the seeded homepage copy from Roman Urdu to English on existing installs.
 * Each row changes only if it still holds the original text, so admin edits are kept.
 */
return new class extends Migration
{
    private const SETTINGS = [
        'tagline' => [
            'Pakistan ke liye jobs, schemes aur education ki information website.',
            'Trusted information on jobs, government schemes and education in Pakistan.',
        ],
        'hero_title' => [
            'Jobs, Schemes, BISP aur Education ki Sahi Maloomat',
            'Reliable Information on Jobs, Schemes, BISP and Education',
        ],
        'hero_text' => [
            'Latest jobs, government schemes, BISP 8171 updates, admissions aur home decor ideas – sab ek jagah.',
            'Latest jobs, government schemes, BISP 8171 updates, admissions and home decor ideas, all in one place.',
        ],
        'newsletter_title' => ['Roz ki Latest Updates Paayein', 'Get the Latest Updates Every Day'],
        'newsletter_text' => [
            'Jobs, schemes aur BISP updates seedha apne email par.',
            'Jobs, schemes and BISP updates delivered to your inbox.',
        ],
    ];

    private const STEPS = [
        'Category ya search se sahi guide chunein.' => 'Use a category or search to find the right guide.',
        'Shartein aur zaroori documents parhein.' => 'Read the requirements and the documents you need.',
        'Hamari di hui link se official site par tasdeeq karein.' => 'Use our link to confirm the details on the official website.',
        'Last date se pehle apply karein.' => 'Submit your application before the last date.',
    ];

    private const FAQS = [
        'Kya yeh official government website hai?' => [
            'Is this an official government website?',
            'No. This is an information website only. Always confirm details on the official portal.',
        ],
        'BISP 8171 ki information kitni nayi hoti hai?' => [
            'How up to date is the BISP 8171 information?',
            'We check for updates regularly and label older information as "Outdated".',
        ],
        'Kya main apna article bhej sakta hun?' => [
            'Can I submit my own article?',
            'Yes. Please get in touch through our "Write for Us" page.',
        ],
    ];

    private const POST_TITLES = [
        '8171 Eligibility Guide: Kaun Apply Kar Sakta Hai?' => '8171 Eligibility Guide: Who Can Apply?',
        'CNIC Status Check Karne ka Tareeqa' => 'How to Check Your CNIC Status',
        'BISP Official Portal Use Karne ki Guide' => 'How to Use the BISP Official Portal',
        'BISP Latest Updates – Kya Naya Hai?' => 'BISP Latest Updates: What Is New?',
        'Results & Merit Lists – Kaise Check Karein' => 'Results & Merit Lists: How to Check Them',
    ];

    private const BLOG_POSTS = [
        'Government Scheme ke Liye Apply Karne se Pehle 7 Zaroori Baatein' => [
            '7 Things to Know Before Applying for a Government Scheme',
            'Documents, eligibility and the common mistakes that get applications rejected.',
        ],
        'CV Kaise Banayein: Pakistan Jobs ke Liye Tips' => [
            'How to Write a CV: Tips for Jobs in Pakistan',
            'Simple ways to build a clear and effective CV.',
        ],
        'Entry Test ki Tayari: 30 Din ka Plan' => [
            'Entry Test Preparation: A 30-Day Plan',
            'A simple month-long study plan with revision tips.',
        ],
        'Kam Budget mein Ghar ko Naya Look Dein' => [
            'Give Your Home a New Look on a Small Budget',
            'Affordable ideas to refresh your home.',
        ],
    ];

    public function up(): void
    {
        foreach (self::SETTINGS as $key => [$old, $new]) {
            $current = DB::table('settings')->where('key', $key)->value('value');

            if ($current !== null && json_decode($current) === $old) {
                DB::table('settings')->where('key', $key)->update(['value' => json_encode($new)]);
            }
        }

        $this->translateSteps();

        foreach (self::FAQS as $old => [$question, $answer]) {
            DB::table('faqs')->where('question', $old)->update(['question' => $question, 'answer' => $answer]);
        }

        foreach (self::POST_TITLES as $old => $new) {
            DB::table('posts')->where('title', $old)->update(['title' => $new, 'slug' => Str::slug($new)]);
        }

        DB::table('posts')->where('excerpt', 'like', '% ke baare mein zaroori maloomat, shartein aur aglay qadam.')
            ->orderBy('id')
            ->each(fn (object $post) => DB::table('posts')->where('id', $post->id)->update([
                'excerpt' => "A guide to {$post->title}: key details, requirements and next steps.",
            ]));

        foreach (self::BLOG_POSTS as $old => [$title, $excerpt]) {
            DB::table('blog_posts')->where('title', $old)->update([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => $excerpt,
            ]);
        }
    }

    private function translateSteps(): void
    {
        $current = DB::table('settings')->where('key', 'steps')->value('value');

        if ($current === null) {
            return;
        }

        $steps = array_map(function (array $step) {
            $step['text'] = self::STEPS[$step['text']] ?? $step['text'];

            return $step;
        }, json_decode($current, true) ?? []);

        DB::table('settings')->where('key', 'steps')->update(['value' => json_encode($steps)]);
    }
};
