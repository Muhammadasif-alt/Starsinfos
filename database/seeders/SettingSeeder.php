<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'tagline' => 'Trusted information on jobs, government schemes, careers and style in Pakistan.',
            'footer_text' => 'Find government, private, bank and teaching jobs in Pakistan, with last dates and links to official sources. We also share government schemes, career advice and beauty and style tips. This is an information website, not an official government portal, so always confirm details with the official source.',
            'hero_title' => 'Worldwide Jobs and Beauty & Style Tips, All in One Place',
            'hero_highlight' => 'Worldwide Jobs',
            'hero_text' => 'Latest jobs in Pakistan and abroad, government schemes, career advice and beauty and style tips, explained simply with links to official sources.',
            'hero_chips' => [
                ['label' => 'Latest Jobs', 'url' => '/jobs'],
                ['label' => 'Government Schemes', 'url' => '/government-schemes'],
                ['label' => 'Beauty & Style', 'url' => '/beauty-style'],
                ['label' => 'Career Advice', 'url' => '/blog'],
            ],
            'stats' => [
                ['value' => '3', 'label' => 'Main Categories'],
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
            'newsletter_text' => 'Jobs, schemes and style updates delivered to your inbox.',
            'social_links' => [],
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
