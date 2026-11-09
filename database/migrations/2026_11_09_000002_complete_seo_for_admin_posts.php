<?php

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Post;
use App\Support\ArticleHtml;
use Illuminate\Database\Migrations\Migration;

/**
 * Fills SEO gaps in posts written in the admin panel: section, tags, FAQs, meta
 * descriptions over 160 characters and cut-off summaries. Each value is only written
 * when the field is still empty or broken, so later admin edits are never overwritten.
 * Finally every post is re-saved once so the ArticleHtml clean-up applies to it.
 */
return new class extends Migration
{
    private const CATEGORIES = [
        'minimalist-wardrobe-essentials-for-women' => 'fashion-clothing',
        'best-hairstyles-for-long-hair' => 'hair-care',
        'kisan-card-apply-online-punjab-pakistan' => 'government-financial-assistance',
        'punjab-green-tractor-program' => 'government-financial-assistance',
        'pave-electric-bike-scheme-installments-subsidy-amount-dealer-process-step-by-step' => 'government-financial-assistance',
        'cm-punjab-e-bike-scheme-delivery-payment-status' => 'government-financial-assistance',
        'junior-auditor-bps-11-jobs-test-syllabus-preparation' => 'government-jobs',
        'data-entry-operator-bps-14-jobs-typing-test-speed-requirements-preparation-tips' => 'government-jobs',
        'pakistan-army-soldier-written-test-syllabus' => 'army-police-jobs',
        'cm-punjab-laptop-program-next-phase-what-should-students-do-now-latest-status-guide' => 'student-scholarships',
    ];

    private const TAGS = [
        'pakistan-army-soldier-registration-2026' => ['Pak Army Soldier Registration 2026', 'Army Soldier Age Limit', 'Army Height Requirement', 'Join Pak Army', 'Army Jobs 2026'],
        'pma-159-long-course-initial-test-syllabus-medical-issb' => ['PMA 159 Long Course', 'PMA Initial Test', 'ISSB Preparation', 'Pak Army Officer', 'PMA Medical Test'],
        'minimalist-wardrobe-essentials-for-women' => ['Minimalist Wardrobe', 'Capsule Wardrobe', "Women's Fashion", 'Wardrobe Essentials'],
        'best-hairstyles-for-long-hair' => ['Long Hairstyles', 'Hairstyles for Women', 'Braids', 'Hair Care Tips'],
        'kisan-card-apply-online-punjab-pakistan' => ['Kisan Card Punjab', 'Kissan Card Apply Online', 'Farmer Loans Punjab', 'Punjab Agriculture Schemes'],
        'punjab-green-tractor-program' => ['Green Tractor Program', 'CM Punjab Tractor Scheme', 'Tractor Subsidy Punjab', 'Farmer Schemes'],
        'junior-auditor-bps-11-jobs-test-syllabus-preparation' => ['Junior Auditor Jobs', 'BPS-11 Jobs', 'Junior Auditor Test Syllabus', 'Government Jobs Pakistan'],
        'data-entry-operator-bps-14-jobs-typing-test-speed-requirements-preparation-tips' => ['Data Entry Operator Jobs', 'BPS-14 Jobs', 'Typing Test', 'Government Jobs Pakistan'],
        'pave-electric-bike-scheme-installments-subsidy-amount-dealer-process-step-by-step' => ['PAVE Electric Bike Scheme', 'E-Bike Installments', 'Electric Bike Subsidy', 'Punjab Schemes'],
        'small-garden-design-ideas' => ['Small Garden Design', 'Garden Ideas', 'Home Garden', 'Outdoor Decor'],
        'modern-backyard-garden-design-ideas-that-look-expensive-on-a-budget' => ['Backyard Garden Ideas', 'Budget Garden Design', 'Garden Ideas', 'Outdoor Decor'],
        'pakistan-army-soldier-written-test-syllabus' => ['Army Soldier Written Test', 'Pak Army Test Syllabus', 'Army Jobs 2026', 'Join Pak Army'],
        'cm-punjab-laptop-program-next-phase-what-should-students-do-now-latest-status-guide' => ['CM Punjab Laptop Scheme', 'Laptop Program Next Phase', 'Student Schemes Punjab', 'Laptop Scheme Status'],
        'cm-punjab-e-bike-scheme-delivery-payment-status' => ['CM Punjab E-Bike Scheme', 'E-Bike Delivery Status', 'E-Bike Installments', 'Student Schemes Punjab'],
        'front-yard-garden-design-ideas' => ['Front Yard Garden Ideas', 'Curb Appeal', 'Garden Design', 'Outdoor Decor'],
        '15-best-beauty-tips-for-women' => ['Beauty Tips for Women', 'Natural Beauty Tips', 'Skincare Tips', 'Glowing Skin'],
        'best-skincare-routine-for-beginners-a-step-by-step-guide' => ['Skincare Routine for Beginners', 'Skincare Steps', 'Skincare Tips', 'Glowing Skin'],
        'latest-womens-fashion-trends-in-2026-outfit-ideas-and-styling-tips' => ["Women's Fashion Trends 2026", 'Outfit Ideas', 'Styling Tips', "Women's Fashion"],
    ];

    private const META_DESCRIPTIONS = [
        'pma-159-long-course-initial-test-syllabus-medical-issb' => 'PMA 159 Long Course guide: initial test syllabus, eligibility, physical and medical tests, ISSB selection process, documents and preparation tips.',
        'pakistan-army-soldier-written-test-syllabus' => 'Pakistan Army Soldier Written Test 2026: syllabus, subjects, passing marks, paper pattern and preparation tips, with the official apply link.',
        'cm-punjab-e-bike-scheme-delivery-payment-status' => 'CM Punjab E-Bike Scheme 2026: check application status, balloting, delivery updates, monthly installments and what selected students should do next.',
        'front-yard-garden-design-ideas' => "Front yard garden design ideas with flowers, plants, pathways and lighting to boost your home's curb appeal and create a welcoming first impression.",
        '15-best-beauty-tips-for-women' => '15 simple beauty tips for women covering skincare, hair care, healthy habits and natural remedies for glowing skin and natural beauty.',
    ];

    private const FAQS = [
        'best-hairstyles-for-long-hair' => [
            ['question' => 'Which hairstyle suits long hair best?', 'answer' => 'It depends on your hair texture and the occasion. Long layers add movement while keeping length, sleek straight hair or a low bun suit formal events, and beach waves or space buns work well for casual days and festivals.'],
            ['question' => 'How often should long hair be trimmed?', 'answer' => 'A light trim, often called a dusting, every 8 to 12 weeks helps stop split ends from travelling up the hair shaft, even while you are growing your hair out.'],
            ['question' => 'How can I protect long hair from styling damage?', 'answer' => 'Use a heat protectant spray before blow dryers, flat irons or curling wands, deep condition once a week, and switch to silk or satin pillowcases and scrunchies to reduce friction and breakage.'],
            ['question' => 'Which long hairstyle is the lowest maintenance?', 'answer' => 'A long messy shag. It looks better when it is not perfectly done, and a little texturising spray or dry shampoo is usually enough to refresh the layers.'],
        ],
        'cm-punjab-e-bike-scheme-delivery-payment-status' => [
            ['question' => 'Is CM Punjab E-Bike Scheme registration still open?', 'answer' => 'No. Phase II applications were accepted on the official portal until 4 October 2026. Existing applicants should keep checking their accounts for verification, selection and delivery updates.'],
            ['question' => 'How can I check my E-Bike application status?', 'answer' => 'Log in at bikes.punjab.gov.pk with your registered CNIC and password, then open your dashboard to see licence verification, student verification and bank status.'],
            ['question' => 'Does applying guarantee an electric bike?', 'answer' => 'No. More than 600,000 applications were received for 100,000 bikes, so students are selected through balloting and must then clear verification and bank approval.'],
            ['question' => 'What is the monthly installment for the E-Bike?', 'answer' => 'About Rs. 3,028 a month for 36 months with zero down payment, after a government subsidy of Rs. 90,000. Confirm the final repayment schedule with the Bank of Punjab.'],
            ['question' => 'When will students receive their E-Bikes?', 'answer' => 'Distribution was announced to start from 5 November 2026, but each bike is delivered only after verification, selection and financing are complete. Wait for official confirmation before visiting a collection centre.'],
        ],
    ];

    public function up(): void
    {
        $categories = Category::pluck('id', 'slug');

        Post::withoutEvents(function () use ($categories) {
            foreach (Post::whereIn('slug', array_keys(self::CATEGORIES + self::TAGS + self::META_DESCRIPTIONS + self::FAQS))->get() as $post) {
                $slug = $post->slug;

                if (isset(self::CATEGORIES[$slug], $categories[self::CATEGORIES[$slug]])) {
                    $post->category_id = $categories[self::CATEGORIES[$slug]];
                }
                if (empty($post->tags) && isset(self::TAGS[$slug])) {
                    $post->tags = self::TAGS[$slug];
                }
                if (mb_strlen((string) $post->meta_description) > 160 && isset(self::META_DESCRIPTIONS[$slug])) {
                    $post->meta_description = self::META_DESCRIPTIONS[$slug];
                }
                if (empty($post->faqs) && isset(self::FAQS[$slug])) {
                    $post->faqs = self::FAQS[$slug];
                }

                $post->timestamps = false;
                $post->save();
            }
        });

        // Re-save everything once so headings, alt text, typed FAQ sections and summaries are tidied.
        foreach ([Post::class, BlogPost::class] as $model) {
            $model::query()->each(function ($post) {
                if ($this->summaryIsCutOff($post->excerpt)) {
                    $post->excerpt = mb_strlen((string) $post->meta_description) <= 160 && $post->meta_description
                        ? $post->meta_description
                        : ArticleHtml::summary($post->body);
                }

                // save() (not saveQuietly) so the NormalizesArticle hook runs; keep the real "Updated" date.
                $post->timestamps = false;
                $post->save();
            });
        }
    }

    private function summaryIsCutOff(?string $excerpt): bool
    {
        $excerpt = trim((string) $excerpt);

        return $excerpt === '' || (mb_strlen($excerpt) >= 290 && ! preg_match('/[.!?…]$/u', $excerpt));
    }

    public function down(): void
    {
        // Content clean-up; nothing to undo.
    }
};
