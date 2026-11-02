<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'tagline' => 'Trusted information on jobs, government schemes and education in Pakistan.',
            'hero_title' => 'Find Your Next Job in Pakistan',
            'hero_text' => 'Government, private, bank and teaching jobs with last dates and official sources, plus schemes and admissions in one place.',
            'hero_chips' => [
                ['label' => 'Government Jobs', 'url' => '/jobs/government-jobs'],
                ['label' => 'Bank Jobs', 'url' => '/jobs/bank-jobs'],
                ['label' => 'Teaching Jobs', 'url' => '/jobs/teaching-jobs'],
                ['label' => 'Work From Home', 'url' => '/jobs/online-work-from-home-jobs'],
            ],
            'stats' => [
                ['value' => '5', 'label' => 'Main Categories'],
                ['value' => '50+', 'label' => 'Topics Covered'],
                ['value' => 'Daily', 'label' => 'Fresh Updates'],
                ['value' => '100%', 'label' => 'Verified Sources'],
            ],
            'steps' => [
                ['title' => 'Find Your Topic', 'text' => 'Use a category or search to find the right guide.'],
                ['title' => 'Check Eligibility', 'text' => 'Read the requirements and the documents you need.'],
                ['title' => 'Verify Official Source', 'text' => 'Use our link to confirm the details on the official website.'],
                ['title' => 'Apply', 'text' => 'Submit your application before the last date.'],
            ],
            'disclaimer' => 'This is an information website, not an official government portal.',
            'newsletter_title' => 'Get the Latest Updates Every Day',
            'newsletter_text' => 'Jobs, schemes and BISP updates delivered to your inbox.',
            'social_links' => [],
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
