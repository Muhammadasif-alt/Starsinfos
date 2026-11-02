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
            'footer_text' => 'Find government, private, bank and teaching jobs in Pakistan, with last dates and links to official sources. We also share career advice, government schemes and admissions to help you take your next step. This is an information website, not an official government portal, so always confirm details with the official source.',
            'hero_title' => 'Your Trusted Guide to Jobs, Schemes & Education in Pakistan',
            'hero_highlight' => 'Trusted Guide',
            'hero_text' => 'Latest jobs, government schemes, BISP 8171, admissions and career advice from across Pakistan, explained simply with links to official sources.',
            'hero_chips' => [
                ['label' => 'Latest Jobs', 'url' => '/jobs'],
                ['label' => 'Government Schemes', 'url' => '/government-schemes'],
                ['label' => 'BISP 8171', 'url' => '/bisp-8171'],
                ['label' => 'Admissions', 'url' => '/education'],
                ['label' => 'Career Advice', 'url' => '/blog'],
            ],
            'stats' => [
                ['value' => '4', 'label' => 'Main Categories'],
                ['value' => '50+', 'label' => 'Topics Covered'],
                ['value' => 'Daily', 'label' => 'Fresh Updates'],
                ['value' => '100%', 'label' => 'Verified Sources'],
            ],
            'steps' => [
                ['title' => 'Find Your Topic', 'text' => 'Use a category or search to find the right guide.'],
                ['title' => 'Check Eligibility', 'text' => 'Read the requirements and the documents you need.'],
                ['title' => 'Verify Official Source', 'text' => 'Use our link to confirm the details on the official website.'],
                ['title' => 'Apply', 'text' => 'Submit your application before the last date.'],
                ['title' => 'Track Your Application', 'text' => 'Save your application slip and check the portal for test and interview dates.'],
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
