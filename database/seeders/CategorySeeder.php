<?php

namespace Database\Seeders;

use App\Models\Category;
use Database\Seeders\Concerns\SeedsImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    use SeedsImages;

    private const TREE = [
        ['Jobs', 'jobs', '#2563EB', '💼', 'jobs-banner', 'Govt, Bank, Army & Police, Teaching, FPSC & PPSC', [
            'Government Jobs', 'Private Jobs', 'Bank Jobs', 'Army & Police Jobs', 'Teaching Jobs',
            'WAPDA & Railway Jobs', 'FPSC & PPSC Jobs', 'Online & Work From Home Jobs', 'Latest Jobs in Pakistan',
        ]],
        ['Government Schemes', 'government-schemes', '#0B6B4F', '🏛️', 'schemes-banner', 'Loans, Housing, Solar, Scholarships', [
            'Government Financial Assistance', 'Housing Schemes', 'Youth Loan Schemes', 'Solar Schemes',
            'Student Scholarships', 'Business Support Programs', 'Registration Guides', 'Eligibility & Application Process',
        ]],
        ['BISP 8171', 'bisp-8171', '#7C3AED', '💳', 'bisp-banner', 'Eligibility, Payments, CNIC Status, Survey', [
            'BISP Latest Updates', '8171 Eligibility Guide', 'Payment Updates', 'Registration & Dynamic Survey',
            'CNIC Status Check Guide', 'BISP Beneficiary Information', 'Official Portal Guides',
        ]],
        ['News', 'news', '#D13B3B', '📰', 'news-banner', 'Pakistan, Govt Updates, Business, Tech', [
            'Pakistan News', 'Government Updates', 'Education News', 'Jobs News',
            'Technology News', 'Business News', 'Public Announcements',
        ]],
        ['Education & Admissions', 'education', '#0E8FA3', '🎓', 'education-banner', 'BS, University, Entry Tests, Merit Lists', [
            'BS Admissions', 'University Admissions', 'College Admissions', 'Entry Tests',
            'Results & Merit Lists', 'Scholarships', 'Matric & Intermediate', 'Degree Programs',
        ]],
        ['Home & Decor', 'home-decor', '#C2652A', '🛋️', 'decor-banner', 'Living Room, Kitchen, Garden, DIY Ideas', [
            'Indoor Home Decor', 'Outdoor Home Decor', 'Living Room Ideas', 'Bedroom Design', 'Kitchen Design',
            'Bathroom Design', 'Garden & Balcony', 'Furniture & Lighting', 'Small House Design', 'DIY Decoration Ideas',
        ]],
    ];

    /** Subcategories shown as gallery tiles on the homepage: slug => [image, tile colour]. */
    private const DECOR_TILES = [
        'living-room-ideas' => ['decor-living-ideas', '#C2652A'],
        'garden-balcony' => ['decor-neutral-lounge', '#0B6B4F'],
        'kitchen-design' => ['decor-window-seat', '#7C5A3A'],
        'bedroom-design' => ['decor-green-lounge', '#0E8FA3'],
        'small-house-design' => ['decor-warm-living', '#B4572F'],
        'diy-decoration-ideas' => ['decor-classic-lounge', '#5B6B64'],
    ];

    private const JOB_ICONS = [
        'government-jobs' => '🏛️',
        'private-jobs' => '🏢',
        'bank-jobs' => '🏦',
        'army-police-jobs' => '🛡️',
        'teaching-jobs' => '📚',
        'wapda-railway-jobs' => '⚡',
        'fpsc-ppsc-jobs' => '📝',
        'online-work-from-home-jobs' => '💻',
        'latest-jobs-in-pakistan' => '🔔',
    ];

    public function run(): void
    {
        foreach (self::TREE as $order => [$name, $slug, $color, $icon, $image, $description, $children]) {
            $root = Category::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => $description,
                'color' => $color,
                'icon' => $icon,
                'image' => $this->seedImage($image),
                'sort_order' => $order,
            ]);

            foreach ($children as $childOrder => $childName) {
                $childSlug = Str::slug($childName);
                [$tileImage, $tileColor] = self::DECOR_TILES[$childSlug] ?? [null, $color];

                Category::updateOrCreate(['slug' => $childSlug], [
                    'parent_id' => $root->id,
                    'name' => $childName,
                    'color' => $tileColor,
                    'icon' => self::JOB_ICONS[$childSlug] ?? null,
                    'image' => $this->seedImage($tileImage),
                    'sort_order' => $childOrder,
                ]);
            }
        }
    }
}
