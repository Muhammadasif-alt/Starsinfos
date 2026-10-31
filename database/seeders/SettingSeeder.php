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
            'hero_title' => 'Reliable Information on Jobs, Schemes, BISP and Education',
            'hero_text' => 'Latest jobs, government schemes, BISP 8171 updates, admissions and home decor ideas, all in one place.',
            'hero_chips' => [
                ['label' => 'Latest Jobs in Pakistan', 'url' => '/jobs/latest-jobs-in-pakistan'],
                ['label' => '8171 Eligibility', 'url' => '/bisp-8171/8171-eligibility-guide'],
                ['label' => 'Entry Tests', 'url' => '/education/entry-tests'],
                ['label' => 'Solar Schemes', 'url' => '/government-schemes/solar-schemes'],
            ],
            'stats' => [
                ['value' => '6', 'label' => 'Main Categories'],
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
