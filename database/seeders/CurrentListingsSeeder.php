<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Real listings checked against official sources on 2 Nov 2026, replacing sample jobs and schemes.
 * Run on its own, once: php artisan db:seed --class=CurrentListingsSeeder --force
 */
class CurrentListingsSeeder extends Seeder
{
    private const NJP = 'https://www.njp.gov.pk/jobs/live';

    private const CHECKED = '<p><em>Last checked against the official source on 2 November 2026. Always confirm the eligibility, quota and last date in the official advertisement before applying. Never pay an agent to apply.</em></p>';

    private const NJP_STEPS = '<h2>How to Apply</h2><ol><li>Open the National Jobs Portal using the source link below.</li><li>Sign in or create your account.</li><li>Search for the post and read its advertisement.</li><li>Apply before the last date.</li></ol>';

    /** Slugs of seeded sample posts that real listings replace. */
    private const SAMPLES = [
        'government-jobs-clerk-assistant-posts', 'bank-jobs-officer-grade-hiring', 'army-police-constable-recruitment',
        'teaching-jobs-fpsc-ppsc-updates', 'work-from-home-online-jobs', 'healthcare-jobs-in-private-hospitals',
        'solar-scheme-registration-guide', 'youth-loan-scheme-eligibility-how-to-apply', 'free-laptop-scheme-for-students-complete-guide',
        'green-tractor-program-farmers-guide', 'housing-schemes-application-process', 'government-scheme-eligibility-common-requirements',
    ];

    public function run(): void
    {
        // Only touches seeded samples, so featured and trending choices made in the admin survive a rerun.
        Post::whereIn('slug', [...self::SAMPLES, '8171-eligibility-guide-who-can-apply'])->update(['is_featured' => false, 'is_trending' => false]);
        Post::whereIn('slug', self::SAMPLES)->update(['status' => 'draft']);

        $this->publishSchemeGuides();

        $author = User::where('email', 'admin.author@example.com')->firstOrFail();
        $bankSchemes = $this->bankSchemesCategory();

        foreach ([...$this->jobs(), ...$this->bankSchemes()] as $index => $post) {
            Post::firstOrCreate(['slug' => Str::slug($post['title'])], [
                'category_id' => $post['category_id'] ?? Category::where('slug', $post['category'] ?? $bankSchemes->slug)->value('id'),
                'author_id' => $author->id,
                'title' => $post['title'],
                'excerpt' => $post['excerpt'],
                'body' => $post['body'].self::CHECKED,
                'source_url' => $post['source'],
                'closing_date' => $post['closing'] ?? null,
                'department' => $post['department'] ?? null,
                'location' => $post['location'] ?? null,
                'job_type' => $post['job_type'] ?? null,
                'is_featured' => $post['featured'] ?? false,
                'is_trending' => $post['trending'] ?? false,
                'status' => 'published',
                'published_at' => now()->subMinutes($index),
            ]);
        }
    }

    private function publishSchemeGuides(): void
    {
        $images = [
            'cm-punjab-free-solar-panel-scheme-eligibility-and-application-result' => 'seed/scheme-solar.jpg',
            'cm-punjab-laptop-program-phase-2-eligibility-and-latest-status' => 'seed/scheme-laptops.jpg',
        ];

        Post::whereIn('slug', [
            'pm-youth-chromebook-scheme-eligibility-and-how-to-apply',
            'apni-chhat-apna-ghar-interest-free-home-construction-loan-in-punjab',
            'apni-chhat-mehfooz-chhat-interest-free-loan-for-roof-repair-or-an-extra-room',
            'pave-electric-bike-and-rickshaw-program-subsidy-and-eligibility',
            'cm-punjab-e-bike-scheme-phase-2-for-students-terms-and-status',
            'cm-punjab-free-solar-panel-scheme-eligibility-and-application-result',
            'cm-punjab-laptop-program-phase-2-eligibility-and-latest-status',
        ])->where('status', 'draft')->get()->each(fn (Post $post) => $post->update([
            'status' => 'published',
            'published_at' => now(),
            'featured_image' => $post->featured_image ?? ($images[$post->slug] ?? null),
            'application_status' => [
                'pm-youth-chromebook-scheme-eligibility-and-how-to-apply' => 'Open',
                'apni-chhat-apna-ghar-interest-free-home-construction-loan-in-punjab' => 'Open',
                'apni-chhat-mehfooz-chhat-interest-free-loan-for-roof-repair-or-an-extra-room' => 'Open',
                'cm-punjab-free-solar-panel-scheme-eligibility-and-application-result' => 'Closed',
            ][$post->slug] ?? null,
            'is_trending' => in_array($post->slug, [
                'pm-youth-chromebook-scheme-eligibility-and-how-to-apply',
                'apni-chhat-apna-ghar-interest-free-home-construction-loan-in-punjab',
            ], true),
        ]));
    }

    private function bankSchemesCategory(): Category
    {
        $root = Category::where('slug', 'government-schemes')->firstOrFail();

        return Category::firstOrCreate(['slug' => 'bank-schemes-loans'], [
            'parent_id' => $root->id,
            'name' => 'Bank Schemes & Loans',
            'color' => $root->color,
            'icon' => 'banknote',
            'sort_order' => 8,
        ]);
    }

    /** @param array<string, string> $facts */
    private function facts(array $facts): string
    {
        $rows = collect($facts)->map(fn (string $value, string $label) => "<li><strong>{$label}:</strong> {$value}</li>")->implode('');

        return "<h2>Job Details</h2><ul>{$rows}</ul>";
    }

    private function jobs(): array
    {
        return [
            [
                'title' => 'Islamabad Police Dolphin Force Jobs 2026: Executive Staff',
                'category' => 'army-police-jobs',
                'source' => 'https://npftas.pk/',
                'closing' => '2026-11-15',
                'featured' => true,
                'department' => 'Islamabad Capital Territory Police',
                'location' => 'Islamabad',
                'job_type' => 'Permanent',
                'excerpt' => 'Islamabad Capital Territory Police · Sub Inspector, ASI and Constable posts · Apply through NPF-TAS',
                'body' => '<p>Islamabad Capital Territory Police has opened recruitment for Executive Staff in the Dolphin Force. The official NPF-TAS portal lists 1 November 2026 as the published date and 15 November 2026 as the last date.</p>'
                    .$this->facts(['Organization' => 'Islamabad Capital Territory Police', 'Reported posts' => 'Sub Inspector (BS-14), Assistant Sub Inspector (BS-11) and Constable (BS-07)', 'Reported vacancies' => '1,058', 'Last date' => '15 November 2026'])
                    .'<p>Post details and the vacancy total come from published reports of the advertisement. Check the advertisement on the portal for age, education, physical standards, quotas and any fee.</p>'
                    .'<h2>How to Apply</h2><ol><li>Open the NPF-TAS portal using the source link below.</li><li>Read the advertisement.</li><li>Register and submit your application before the last date.</li></ol>',
            ],
            [
                'title' => 'Pakistan Army Soldier Recruitment: Registration Open',
                'category' => 'army-police-jobs',
                'source' => 'https://www.joinpakarmy.gov.pk/',
                'closing' => '2026-11-23',
                'trending' => true,
                'department' => 'Pakistan Army',
                'location' => 'All Pakistan',
                'job_type' => 'Permanent',
                'excerpt' => 'Pakistan Army · Recruitment as a Soldier · Registration on Join Pak Army',
                'body' => '<p>The official Join Pak Army website shows that registration for Recruitment as a Soldier is open until 23 November 2026.</p>'
                    .$this->facts(['Organization' => 'Pakistan Army', 'Post' => 'Soldier', 'Last date' => '23 November 2026'])
                    .'<h2>How to Apply</h2><ol><li>Open Join Pak Army using the source link below.</li><li>Select the soldier recruitment course and read its eligibility and medical standards.</li><li>Complete your registration before the last date.</li></ol>',
            ],
            [
                'title' => 'PMA 159 Long Course: Join Pakistan Army as an Officer',
                'category' => 'army-police-jobs',
                'source' => 'https://www.joinpakarmy.gov.pk/',
                'department' => 'Pakistan Army',
                'location' => 'All Pakistan',
                'job_type' => 'Permanent',
                'excerpt' => 'Pakistan Army · Officer training course · Applications open on Join Pak Army',
                'body' => '<p>The official Join Pak Army website lists PMA 159 Long Course (September 2026) as open for applications. Its homepage does not show a closing date, so check the course page before you apply.</p>'
                    .$this->facts(['Organization' => 'Pakistan Army', 'Course' => 'PMA 159 Long Course'])
                    .'<h2>How to Apply</h2><ol><li>Open Join Pak Army using the source link below.</li><li>Read the course eligibility and medical standards.</li><li>Submit your application.</li></ol>',
            ],
            $this->njpJob('Junior Auditor Jobs (BPS-11): Controller General of Accounts, Balochistan', 'government-jobs', 'Office of the Controller General of Accounts', 'BPS-11', '39', 'Regular', 'Balochistan', '2026-11-26', true),
            $this->njpJob('Assistant Divisional Engineer (BPS-17): NTC, Balochistan', 'government-jobs', 'National Telecommunication Corporation', 'BPS-17', '1', 'Regular', 'Balochistan', '2026-11-17'),
            $this->njpJob('Assistant Private Secretary (BPS-16): NTC, Balochistan', 'government-jobs', 'National Telecommunication Corporation', 'BPS-16', '1', 'Regular', 'Balochistan', '2026-11-17'),
            $this->njpJob('Superintendent (BPS-16): Drug Regulatory Authority, Balochistan', 'government-jobs', 'Drug Regulatory Authority of Pakistan', 'BPS-16', '1', 'Regular', 'Balochistan', '2026-11-15', false, '5 years or more'),
            $this->njpJob('Lower Division Clerk (BPS-09): Drug Regulatory Authority, Balochistan', 'government-jobs', 'Drug Regulatory Authority of Pakistan', 'BPS-09', '1', 'Regular', 'Balochistan', '2026-11-15'),
            $this->njpJob('Data Entry Operator (BPS-14): Drug Regulatory Authority, Balochistan', 'government-jobs', 'Drug Regulatory Authority of Pakistan', 'BPS-14', '1', 'Regular', 'Balochistan', '2026-11-15'),
            $this->njpJob('Meteorological Assistant (BS-14): PMD, Balochistan', 'government-jobs', 'Pakistan Meteorological Department', 'BS-14', '2', 'Regular', 'Balochistan', '2026-11-12'),
            $this->njpJob('Staff Car Driver (BPS-04): Cabinet Division, Balochistan', 'government-jobs', 'Cabinet Division', 'BPS-04', '5', 'Regular', 'Balochistan', '2026-11-10'),
            $this->njpJob('Stenotypist (BPS-14): Cabinet Division, Balochistan', 'government-jobs', 'Cabinet Division', 'BPS-14', '2', 'Regular', 'Balochistan', '2026-11-10'),
            $this->njpJob('Junior Accountant (BPS-11): Cadet College Hasanabdal', 'teaching-jobs', 'Cadet College Hasanabdal', 'BPS-11', '1', 'Contract', null, '2026-11-15', false, '2 years or more', 'Hasan Abdal'),
            $this->njpJob('Manager Legal: Pakistan National Shipping Corporation', 'government-jobs', 'Pakistan National Shipping Corporation', 'Grade-12', '1', 'Contract', null, '2026-11-15', false, '10 years or more'),
            $this->njpJob('Financial Analyst (Gas): Petroleum Division', 'government-jobs', 'Petroleum Division, Ministry of Energy', 'SPPS-IV', '1', 'Contract', null, '2026-11-08', false, '3 years or more'),
            $this->njpJob('Senior Officer Finance & Accounts: EXIM Bank of Pakistan', 'bank-jobs', 'EXIM Bank of Pakistan', 'M6', '1', 'Regular', null, '2026-11-04', false, '3 years or more', 'Islamabad'),
        ];
    }

    private function njpJob(string $title, string $category, string $employer, string $scale, string $vacancies, string $type, ?string $listedUnder, string $closing, bool $trending = false, ?string $experience = null, ?string $location = null): array
    {
        $facts = array_filter([
            'Organization' => $employer,
            'Pay scale' => $scale,
            'Vacancies' => $vacancies,
            'Job type' => $type,
            'Location' => $location,
            'Listed under' => $listedUnder,
            'Experience' => $experience,
            'Last date' => date('j F Y', strtotime($closing)),
        ]);

        $note = $listedUnder ? "<p>This post is listed under {$listedUnder} on the portal. Check the domicile requirement in the advertisement before applying.</p>" : '';

        return [
            'title' => $title,
            'category' => $category,
            'source' => self::NJP,
            'closing' => $closing,
            'trending' => $trending,
            'department' => $employer,
            'location' => $location ?? $listedUnder,
            'job_type' => $type === 'Regular' ? 'Permanent' : $type,
            'excerpt' => "{$employer} · {$scale} · {$vacancies} ".Str::plural('vacancy', (int) $vacancies)." · {$type}",
            'body' => "<p>{$employer} has listed this post on the National Jobs Portal, the Government of Pakistan's job platform.</p>".$this->facts($facts).$note.self::NJP_STEPS,
        ];
    }

    private function bankSchemes(): array
    {
        return [
            [
                'title' => 'PM Youth Business and Agriculture Loan Scheme Through Banks',
                'source' => 'https://www.sbp.org.pk/smefd/circulars/2022/C12.htm',
                'excerpt' => 'State Bank of Pakistan scheme offering youth business and agriculture loans through participating banks.',
                'body' => '<p>The Prime Minister\'s Youth Business and Agriculture Loan Scheme offers subsidized financing through participating banks.</p><h2>Loan Tiers in the SBP Circular</h2><ul><li>Tier 1: up to Rs. 0.5 million at 0%, for up to 3 years.</li><li>Tier 2: above Rs. 0.5 million and up to Rs. 1.5 million at 5%, for up to 8 years.</li><li>Tier 3: above Rs. 1.5 million and up to Rs. 7.5 million at 7%, for up to 8 years.</li></ul><h2>Who Can Apply</h2><ul><li>Pakistani citizens with a CNIC, aged 21 to 45.</li><li>For IT and e-commerce, the minimum age is 18 with at least matriculation.</li></ul><p>These terms come from the State Bank of Pakistan circular of December 2022, and later circulars have added to the scheme. Confirm the current terms with the State Bank or a participating bank before applying.</p>',
            ],
            [
                'title' => 'CM Punjab Asaan Karobar Finance: Interest-Free Business Loans',
                'source' => 'https://punjab.gov.pk/node/6372',
                'excerpt' => 'Punjab government business finance through the Bank of Punjab, announced with loans from Rs. 1 million to Rs. 30 million.',
                'body' => '<p>The CM Punjab Asaan Karobar Finance Scheme was launched by the Government of Punjab to support small and medium businesses, with loans provided through the Bank of Punjab.</p><h2>What Was Announced</h2><ul><li>Asaan Karobar Finance: interest-free loans from Rs. 1 million to Rs. 30 million.</li><li>Asaan Karobar Card: Rs. 0.5 million to Rs. 1 million.</li><li>Helpline: 1786.</li></ul><p>These details come from the Punjab Portal announcement of January 2025. Confirm that applications are open and check the current terms through the helpline or the Bank of Punjab.</p>',
            ],
        ];
    }
}
