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
        ['Jobs', 'jobs', '#2563EB', 'briefcase', 'jobs-banner', 'Govt, Bank, Army & Police, Teaching, FPSC & PPSC', [
            'Government Jobs', 'Private Jobs', 'Bank Jobs', 'Army & Police Jobs', 'Teaching Jobs',
            'WAPDA & Railway Jobs', 'FPSC & PPSC Jobs', 'Online & Work From Home Jobs', 'Latest Jobs in Pakistan',
        ]],
        ['Government Schemes', 'government-schemes', '#0B6B4F', 'landmark', 'schemes-banner', 'Loans, Housing, Solar, Scholarships', [
            'Government Financial Assistance', 'Housing Schemes', 'Youth Loan Schemes', 'Solar Schemes',
            'Student Scholarships', 'Business Support Programs', 'Registration Guides', 'Eligibility & Application Process', 'Bank Schemes & Loans',
        ]],
        ['Beauty & Style', 'beauty-style', '#B83280', 'sparkles', null, 'Skincare, makeup, hair care, fashion and grooming tips', [
            'Skincare', 'Makeup', 'Hair Care', 'Nail Care', 'Fashion & Clothing', 'Bridal', "Men's Grooming",
        ]],
    ];

    private const JOB_ICONS = [
        'government-jobs' => 'landmark',
        'private-jobs' => 'building',
        'bank-jobs' => 'banknote',
        'army-police-jobs' => 'shield',
        'teaching-jobs' => 'book',
        'wapda-railway-jobs' => 'zap',
        'fpsc-ppsc-jobs' => 'file',
        'online-work-from-home-jobs' => 'laptop',
        'latest-jobs-in-pakistan' => 'bell',
    ];

    private const SCHEME_ICONS = [
        'government-financial-assistance' => 'wallet',
        'housing-schemes' => 'home',
        'youth-loan-schemes' => 'sprout',
        'solar-schemes' => 'sun',
        'student-scholarships' => 'graduation',
        'business-support-programs' => 'users',
        'registration-guides' => 'clipboard',
        'eligibility-application-process' => 'check',
        'bank-schemes-loans' => 'banknote',
    ];

    private const BEAUTY_ICONS = [
        'skincare' => 'droplet',
        'makeup' => 'sparkles',
        'hair-care' => 'scissors',
        'nail-care' => 'sparkles',
        'fashion-clothing' => 'shirt',
        'bridal' => 'heart',
        'mens-grooming' => 'user',
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

                Category::updateOrCreate(['slug' => $childSlug], [
                    'parent_id' => $root->id,
                    'name' => $childName,
                    'color' => $color,
                    'icon' => self::JOB_ICONS[$childSlug] ?? self::SCHEME_ICONS[$childSlug] ?? self::BEAUTY_ICONS[$childSlug] ?? null,
                    'sort_order' => $childOrder,
                ]);
            }
        }
    }
}
