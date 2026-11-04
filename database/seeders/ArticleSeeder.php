<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\Concerns\SeedsImages;
use Illuminate\Database\Seeder;

/**
 * Articles supplied by the site owner. Each body lives in database/seeders/content/{slug}.html,
 * its FAQs (unless listed here) in {slug}.faqs.json and its banner in database/seeders/images.
 * Articles with a 'model' go to that model (Career Advice); the rest are posts.
 * Existing articles are never touched, so admin edits stay.
 * Run on its own: php artisan db:seed --class=ArticleSeeder --force
 */
class ArticleSeeder extends Seeder
{
    use SeedsImages;

    public function run(): void
    {
        $author = User::where('email', 'contactstarsinfos@gmail.com')->value('id') ?? User::value('id');

        $articles = $this->articles();

        foreach (array_keys($articles) as $index => $slug) {
            $article = $articles[$slug];
            $faqs = database_path("seeders/content/{$slug}.faqs.json");
            $article['faqs'] ??= is_file($faqs) ? json_decode(file_get_contents($faqs), true) : null;
            $category = Category::where('slug', $article['category'])->value('id');

            if (! $category) {
                $this->command?->warn("Skipped {$slug}: no {$article['category']} category");

                continue;
            }

            $model = $article['model'] ?? Post::class;

            $model::firstOrCreate(['slug' => $slug], [
                'category_id' => $category,
                'author_id' => $author,
                'body' => file_get_contents(database_path("seeders/content/{$slug}.html")),
                'featured_image' => $this->seedImage($article['image']),
                'status' => 'published',
                'published_at' => now()->subMinutes(($index + 1) * 10),
            ] + collect($article)->except('model', 'category', 'image')->all());
        }
    }

    private function articles(): array
    {
        return [
            'islamabad-police-dolphin-force-jobs-physical-test-guide' => [
                'category' => 'army-police-jobs',
                'image' => 'dolphin-force-physical-test-2026',
                'title' => 'Islamabad Police Dolphin Force Jobs 2026: Physical Test Requirements & Preparation Guide',
                'meta_title' => 'Islamabad Police Dolphin Force Jobs 2026: Physical Test & How to Apply',
                'meta_description' => 'Dolphin Force 2026: 790 posts, 1.6 km run in 7 minutes (men) or 10 minutes (women), height rules, eligibility, NPF-TAS apply steps and lap-time plan.',
                'excerpt' => '790 Constable, ASI and SI posts. Running test, height and chest standards, eligibility, the NPF-TAS application steps and a lap-by-lap pacing plan.',
                'department' => 'Islamabad Capital Territory Police',
                'location' => 'Islamabad',
                'job_type' => 'Permanent',
                'closing_date' => '2026-11-15',
                'source_url' => 'https://islamabadpolice.gov.pk/notice-detail.php?id=14&type=job',
                'is_trending' => true,
                'tags' => ['Dolphin Force', 'Islamabad Police Jobs', 'Police Physical Test', 'NPF-TAS', 'Constable Jobs 2026'],
                'faqs' => [
                    ['question' => 'Can I apply for constable and ASI together?', 'answer' => 'Check eligibility and the portal procedure for each post separately. The published fee is per post; do not assume one payment or one application enters you for both.'],
                    ['question' => 'Is Indeed an application channel for Dolphin Force?', 'answer' => 'No. Indeed Pakistan can help you discover related listings, but the police notice designates NPF-TAS for this recruitment. Complete Dolphin Force registration on the official NPF-TAS portal.'],
                    ['question' => 'Does passing the run guarantee selection?', 'answer' => 'No. The endurance result addresses one requirement. Follow the instructions for document checks, written assessment, interview, medical assessment and merit announcements as they are issued. Do not treat an unofficial list in a messaging group as an appointment decision.'],
                ],
            ],
            'fpsc-online-apply-2026-step-by-step-guide' => [
                'category' => 'fpsc-ppsc-jobs',
                'image' => 'fpsc-online-apply-2026',
                'title' => 'FPSC Online Apply 2026: Account Banane Se Challan Jama Karne Tak Step by Step Guide',
                'meta_title' => 'FPSC Online Apply 2026: PSID, Fee aur Step-2 Submission Guide',
                'meta_description' => 'FPSC General Recruitment 2026 apply karne ka tareeqa: case number, PSID challan, BS-16 se BS-20 fee, Step-2 submission, Tracking ID aur roll number slip.',
                'excerpt' => 'Sahi post select karein, 17-digit PSID banayein, fee jama karein aur Step-2 submit karein. Fee table, documents aur Tracking ID ki mukammal guide.',
                'source_url' => 'https://www.fpsc.gov.pk/',
                'tags' => ['FPSC Jobs 2026', 'FPSC Online Apply', 'PSID Challan', 'Federal Jobs', 'Government Jobs'],
            ],
            'pakistan-police-constable-recruitment-2026-physical-test' => [
                'category' => 'army-police-jobs',
                'image' => 'police-constable-recruitment-2026',
                'title' => 'Pakistan Police Constable Recruitment 2026: Physical Test, Running Standards & Medical Requirements',
                'meta_title' => 'Police Constable Jobs 2026: Physical Test, Height & Running Standards',
                'meta_description' => 'Compare police constable physical tests in Islamabad, Sindh and Balochistan: height, chest, 1.6 km run times, age limits, documents and how to apply.',
                'excerpt' => 'Height, chest and running standards differ by force. Compare Islamabad, Sindh and Balochistan, then find the official jobs board for every province.',
                'tags' => ['Police Jobs 2026', 'Constable Jobs', 'Police Physical Test', 'Punjab Police', 'Sindh Police'],
            ],
            'bank-mto-jobs-in-pakistan' => [
                'category' => 'bank-jobs',
                'image' => 'bank-mto-jobs-2026',
                'title' => 'Bank MTO Jobs in Pakistan 2026: Eligibility, Test Pattern & Preparation Guide',
                'meta_title' => 'Bank MTO Jobs in Pakistan 2026: Eligibility & Test Guide',
                'meta_description' => 'Bank MTO jobs in Pakistan 2026: eligibility, age limit, salary, test pattern, interview questions, and how to apply on official bank portals.',
                'excerpt' => 'Who can apply for Management Trainee Officer programs, the written test pattern, syllabus, interview questions and official bank career links.',
                'source_url' => 'https://www.abl.com/careers/',
                'tags' => ['Bank MTO Jobs', 'Bank Jobs 2026', 'Management Trainee Officer', 'Fresh Graduate Jobs'],
            ],
            'how-to-find-your-skin-type' => [
                'category' => 'skincare',
                'image' => 'find-your-skin-type',
                'title' => 'How to Find Your Skin Type at Home: Oily, Dry, Combination or Sensitive',
                'meta_title' => 'How to Find Your Skin Type: Easy Home Test',
                'meta_description' => 'Find your skin type at home with a simple bare-face and tissue test. Learn signs of oily, dry, combination, and sensitive skin.',
                'excerpt' => 'Try the 30-minute bare-face and tissue tests, learn the signs of each skin type and build a simple routine that suits your skin.',
                'tags' => ['Skin Type', 'Oily Skin', 'Dry Skin', 'Combination Skin', 'Skincare'],
            ],
            'makeup-that-lasts-in-heat' => [
                'category' => 'makeup',
                'image' => 'makeup-lasts-in-heat',
                'title' => 'How to Make Makeup Last in Hot Weather',
                'meta_title' => 'Makeup That Lasts in Heat: 10 Easy Summer Tips',
                'meta_description' => 'Learn how to make makeup last in hot weather: skin prep, primer, foundation, setting spray, and fixes to stop melting, creasing, and smudging.',
                'excerpt' => 'Light skincare, the right primer, thin layers and smart setting: a step-by-step routine to stop makeup melting in summer heat.',
                'tags' => ['Summer Makeup', 'Long-Lasting Makeup', 'Makeup Tips', 'Oily Skin Makeup'],
            ],
            'hair-fall-in-pakistan' => [
                'category' => 'hair-care',
                'image' => 'hair-fall-in-pakistan',
                'title' => 'Hair Fall in Pakistan: Causes, Hair Care Routine and When to See a Doctor',
                'meta_title' => 'Hair Fall in Pakistan: Causes, Care Routine & Treatment',
                'meta_description' => 'Hair fall in Pakistan: causes, hard water myths, daily care routine, foods, and when to see a dermatologist. Learn what is normal and what is not.',
                'excerpt' => 'What is normal shedding, the common causes of hair fall, the truth about hard water and oils, and when to see a dermatologist.',
                'tags' => ['Hair Fall', 'Hair Care', 'Hair Loss', 'Dermatologist'],
            ],
            'apni-chhat-apna-ghar-required-documents' => [
                'category' => 'housing-schemes',
                'image' => 'apni-chhat-apna-ghar-documents',
                'title' => 'Apni Chhat Apna Ghar Required Documents 2026: Application Checklist for Punjab Residents',
                'meta_title' => 'Apni Chhat Apna Ghar Required Documents & Checklist 2026',
                'meta_description' => 'Apni Chhat Apna Ghar required documents, eligibility, and online application steps for Punjab residents. CNIC, Fard, photos, and tips to avoid rejection.',
                'excerpt' => 'CNIC, Fard and house photos: the documents, eligibility rules and online steps for the Punjab interest-free housing loan.',
                'source_url' => 'https://acag.punjab.gov.pk/',
                'is_trending' => true,
                'tags' => ['Apni Chhat Apna Ghar', 'Punjab Housing Scheme', 'Interest-Free Loan', 'Government Schemes'],
            ],
            'ats-friendly-cv-pakistan' => [
                'model' => BlogPost::class,
                'topic' => 'cv-resume',
                'category' => 'jobs',
                'image' => 'ats-friendly-cv-pakistan',
                'title' => 'How to Make an ATS-Friendly CV for Online Job Applications in Pakistan',
                'meta_title' => 'ATS-Friendly CV Guide for Online Jobs in Pakistan',
                'meta_description' => 'Learn how to make an ATS-friendly CV for online job applications in Pakistan: format, keywords, file type, and what to remove. Includes a simple template.',
                'excerpt' => 'Single-column layout, standard headings, the right file type and job-ad keywords: get your CV read by applicant tracking systems.',
                'is_featured' => true,
                'tags' => ['ATS CV', 'CV Format', 'Resume Tips', 'Online Job Applications', 'Career Advice'],
            ],
            'pm-youth-chromebook-scheme-school-selection' => [
                'category' => 'student-scholarships',
                'image' => 'pm-youth-chromebook-scheme-2026',
                'title' => 'PM Youth Chromebook Scheme 2026: How Are Schools and Students Selected?',
                'meta_title' => 'PM Youth Chromebook Scheme 2026: School Selection Guide',
                'meta_description' => 'PM Youth Chromebook Scheme 2026: how students and schools are selected, eligibility, documents, quotas, and how to apply on the official IBCC portal.',
                'excerpt' => 'Students are picked on Grade 9 merit, not by school lists. Eligibility, province quotas, documents and the IBCC application steps.',
                'source_url' => 'https://pmychromebooks.ibcc.edu.pk/',
                'is_trending' => true,
                'tags' => ['PM Youth Chromebook Scheme', 'Free Laptop Scheme', 'IBCC', 'PM Youth Programme', 'Student Schemes'],
            ],
        ];
    }
}
