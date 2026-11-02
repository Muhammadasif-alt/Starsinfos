<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Real scheme guides, checked against official portals on 2 Nov 2026.
 * Created as drafts to publish once their banners are ready. Run on its own with:
 * php artisan db:seed --class=GovernmentSchemePostSeeder --force
 */
class GovernmentSchemePostSeeder extends Seeder
{
    private const CHECKED = '<p><em>Last checked against the official source on 2 November 2026. Rules can change, so always confirm on the official portal. Never pay an agent to apply.</em></p>';

    public function run(): void
    {
        $author = User::where('email', 'admin.author@example.com')->firstOrFail();

        foreach ($this->posts() as $post) {
            Post::firstOrCreate(['slug' => Str::slug($post['title'])], [
                'category_id' => Category::where('slug', $post['category'])->value('id'),
                'author_id' => $author->id,
                'title' => $post['title'],
                'excerpt' => $post['excerpt'],
                'body' => $post['body'].self::CHECKED,
                'source_url' => $post['source'],
                'closing_date' => $post['closing'] ?? null,
                'status' => 'draft',
                'published_at' => now(),
            ]);
        }
    }

    private function posts(): array
    {
        return [
            [
                'title' => 'PM Youth Chromebook Scheme: Eligibility and How to Apply',
                'category' => 'student-scholarships',
                'source' => 'https://pmychromebooks.ibcc.edu.pk/',
                'excerpt' => 'Top-performing government school students can apply for a Chromebook through the official IBCC portal.',
                'body' => <<<'HTML'
                    <p>The Prime Minister's Youth Chromebook Scheme is for top-performing government school students, based on the Annual Examinations 2026. Applications are made on the official IBCC portal.</p>
                    <h2>Who Can Apply</h2>
                    <ul><li>Punjab, Sindh, Khyber Pakhtunkhwa and Balochistan boards: top-performing Grade 9 (SSC-I) students.</li><li>AJK, Islamabad and Gilgit-Baltistan boards: top-performing Grade 9 and Grade 10 (SSC-II) students.</li><li>The portal states that only students enrolled in government schools are eligible.</li></ul>
                    <h2>Sindh Students</h2>
                    <p>Sindh registration opens board by board after Class 9 results are announced. The portal currently shows BISE Larkano as open, BSE Karachi on hold, and the remaining Sindh boards as coming soon.</p>
                    <h2>Documents You Need</h2>
                    <ul><li>Recent colour photograph with a plain background (JPG or PNG, up to 300 KB).</li><li>SSC-I or SSC-II mark sheet.</li><li>Attested copy of your B-Form or CNIC.</li><li>Attested copy of your father's or guardian's CNIC.</li></ul>
                    <h2>How to Apply</h2>
                    <ol><li>Open the official portal and select your board.</li><li>Enter your CNIC or B-Form, roll or registration number, and your date of birth.</li><li>Check your details, then upload your documents.</li><li>Submit and save your reference number and slip.</li></ol>
                    <p>Each student can apply only once, and the form cannot be changed after submission. If your record is not found, try your roll or registration number, then contact your board.</p>
                    HTML,
            ],
            [
                'title' => 'Apni Chhat Apna Ghar: Interest-Free Home Construction Loan in Punjab',
                'category' => 'housing-schemes',
                'source' => 'https://acag.punjab.gov.pk/',
                'excerpt' => 'Punjab residents who own a small plot can apply for an interest-free loan to build a home.',
                'body' => <<<'HTML'
                    <p>Apni Chhat Apna Ghar is a Government of Punjab housing programme that offers interest-free loans of up to Rs. 1.5 million to build a home, repaid over nine years. The portal reports more than 228,000 loans disbursed.</p>
                    <h2>Who Can Apply</h2>
                    <ul><li>Punjab resident with a CNIC, aged 21 to 60 when applying.</li><li>Sole owner and possessor of land up to 5 marla in urban areas or 10 marla in rural areas.</li><li>No criminal record, and not a loan defaulter.</li></ul>
                    <h2>How to Apply</h2>
                    <ol><li>Open the official portal.</li><li>Select "Apply for Construction of New House".</li><li>Complete the application and follow its instructions.</li></ol>
                    <h2>Early Repayment Rebate</h2>
                    <p>The portal offers a rebate on the outstanding principal for full early repayment: 20% in years 1 to 3, and 10% in years 4 to 6.</p>
                    <h2>Help</h2>
                    <p>Call the toll-free helpline: 0800-09100.</p>
                    HTML,
            ],
            [
                'title' => 'Apni Chhat Mehfooz Chhat: Interest-Free Loan for Roof Repair or an Extra Room',
                'category' => 'housing-schemes',
                'source' => 'https://acag.punjab.gov.pk/',
                'excerpt' => 'Punjab homeowners can apply for an interest-free loan to secure a roof or add a room or floor.',
                'body' => <<<'HTML'
                    <p>Apni Chhat Mehfooz Chhat helps low-income homeowners in Punjab make their homes safer or larger. It was launched in July 2026.</p>
                    <h2>Loan Amount</h2>
                    <ul><li>Up to Rs. 500,000 to repair, strengthen or secure a roof.</li><li>Up to Rs. 1 million to build an additional room or second storey.</li></ul>
                    <p>Reported monthly instalments range from Rs. 4,700 to Rs. 9,300, depending on the loan amount.</p>
                    <h2>Who Can Apply</h2>
                    <ul><li>Punjab resident with a CNIC, aged 21 to 60 when applying.</li><li>Registered with the NSER or PSER with a PMT score of 60 or less.</li><li>Owner of a constructed house up to 5 marla in urban areas or 10 marla in rural areas.</li><li>No criminal record, and not a loan defaulter.</li></ul>
                    <h2>How to Apply</h2>
                    <p>Apply on the official portal under "Apply for Addition of New Floor/Room". The toll-free helpline is 0800-09100.</p>
                    HTML,
            ],
            [
                'title' => 'PAVE Electric Bike and Rickshaw Program: Subsidy and Eligibility',
                'category' => 'government-financial-assistance',
                'source' => 'https://pave.gov.pk/',
                'excerpt' => 'The federal PAVE program offers subsidies on electric bikes, rickshaws and loaders across Pakistan.',
                'body' => <<<'HTML'
                    <p>The Pakistan Accelerated Vehicle Electrification (PAVE) Program is run under the Ministry of Industries and Production. It covers Pakistani citizens nationwide, including AJK and Gilgit-Baltistan.</p>
                    <h2>What the Program Offers</h2>
                    <ul><li>116,000 electric bikes, with a 25% quota for women.</li><li>3,170 electric rickshaws and loaders.</li><li>Bank lease: a Rs. 50,000 subsidy on a bike or Rs. 200,000 on a rickshaw or loader, with the balance in instalments.</li><li>Self finance: the portal lists a subsidy of Rs. 80,000 for a bike or Rs. 400,000 for a rickshaw or loader, paid to your bank account after verification.</li></ul>
                    <h2>Who Can Apply</h2>
                    <ul><li>Electric bikes: ages 18 to 65.</li><li>Electric rickshaws and loaders: ages 21 to 65.</li></ul>
                    <h2>Before You Apply</h2>
                    <p>The portal does not show a clear closing date, so check there that applications are open for your category. You can also call the helpline on 1048.</p>
                    HTML,
            ],
            [
                'title' => 'CM Punjab E-Bike Scheme Phase 2 for Students: Terms and Status',
                'category' => 'government-financial-assistance',
                'source' => 'https://bikes.punjab.gov.pk/',
                'closing' => '2026-10-04',
                'excerpt' => 'Applications for 100,000 student electric scooties closed on 4 October 2026. Here are the scheme terms.',
                'body' => <<<'HTML'
                    <p>Phase 2 of the CM Punjab E-Bike Scheme offers 100,000 electric scooties to students. <strong>The official portal lists 4 October 2026 as the last date to apply.</strong></p>
                    <h2>Scheme Terms</h2>
                    <ul><li>Scooty price: Rs. 199,000, with a Rs. 90,000 government subsidy.</li><li>0% interest, no down payment, and a three-year financing period.</li><li>Monthly instalment of about Rs. 3,000.</li><li>Insurance, registration and token tax are covered by the government, and a helmet and safety rods are free.</li></ul>
                    <h2>Who Was Eligible</h2>
                    <ul><li>Bonafide students aged 16 or older at institutions recognized by the Government of Punjab. Federally chartered institutes were not eligible.</li><li>A valid driving licence, learner's permit or juvenile permit.</li><li>A guarantor with a verifiable monthly income of at least Rs. 40,000, a recent bank statement, and a SIM registered in their own name.</li></ul>
                    HTML,
            ],
            [
                'title' => 'CM Punjab Free Solar Panel Scheme: Eligibility and Application Result',
                'category' => 'solar-schemes',
                'source' => 'https://cmsolarscheme.punjab.gov.pk/',
                'excerpt' => 'Registration is closed. Registered households can check their application result on the official portal.',
                'body' => <<<'HTML'
                    <p>The CM Punjab Free Solar Panel Scheme provides up to 100,000 free solar systems to low-consumption households through balloting. <strong>The official portal says registrations are now closed.</strong> Registered applicants can use its "Application Result" page.</p>
                    <h2>Who Was Eligible</h2>
                    <ul><li>Domestic consumers using up to 200 units a month, based on their June 2024 bill.</li><li>Sanctioned load of up to 2 KW.</li></ul>
                    <h2>Who Was Excluded</h2>
                    <ul><li>Consumers involved in electricity theft.</li><li>Defective meters, or multiple meters at one residence.</li><li>More than three late payments in 12 months.</li></ul>
                    HTML,
            ],
            [
                'title' => 'CM Punjab Laptop Program Phase 2: Eligibility and Latest Status',
                'category' => 'student-scholarships',
                'source' => 'https://cmlaptophed.punjab.gov.pk/',
                'closing' => '2026-04-20',
                'excerpt' => 'Applications for Phase 2 are closed. Here are the eligibility rules and the latest portal updates.',
                'body' => <<<'HTML'
                    <p><strong>Applications for CM Punjab Laptop Program Phase 2 are closed.</strong> The last date was 20 April 2026, and the portal shows 163,326 applications with verification and selection under process.</p>
                    <h2>Who Was Eligible</h2>
                    <ul><li>Domicile from any district of Punjab.</li><li>1st or 2nd semester BS students (admission year 2025), including listed private and federally chartered universities: at least 65% in Intermediate.</li><li>Public-sector MBBS and BDS students (1st professional, session 2025): at least 80%.</li><li>Public-sector DVM students: at least 75%.</li><li>Public-sector BS Nursing students: at least 65%.</li><li>Not already a recipient of any laptop scheme.</li></ul>
                    <p>Blind students are exempt from the marks requirement, and the requirement for non-Muslim BS students is 55%.</p>
                    <h2>What to Do Now</h2>
                    <p>Check the portal and your institute for selection and distribution updates.</p>
                    HTML,
            ],
        ];
    }
}
