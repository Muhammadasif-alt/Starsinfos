<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'tagline' => 'Pakistan ke liye jobs, schemes aur education ki information website.',
            'hero_title' => 'Jobs, Schemes, BISP aur Education ki Sahi Maloomat',
            'hero_text' => 'Latest jobs, government schemes, BISP 8171 updates, admissions aur home decor ideas – sab ek jagah.',
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
                ['title' => 'Find Your Topic', 'text' => 'Category ya search se sahi guide chunein.'],
                ['title' => 'Check Eligibility', 'text' => 'Shartein aur zaroori documents parhein.'],
                ['title' => 'Verify Official Source', 'text' => 'Hamari di hui link se official site par tasdeeq karein.'],
                ['title' => 'Apply', 'text' => 'Last date se pehle apply karein.'],
            ],
            'disclaimer' => 'This is an information website, not an official government portal.',
            'newsletter_title' => 'Roz ki Latest Updates Paayein',
            'newsletter_text' => 'Jobs, schemes aur BISP updates seedha apne email par.',
            'social_links' => [],
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
