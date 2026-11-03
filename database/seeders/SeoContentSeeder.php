<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * Full SEO articles for the listings that have banners. Facts come only from the official
 * sources checked on 2 Nov 2026; everything else is general guidance worded as such.
 * Run on its own: php artisan db:seed --class=SeoContentSeeder --force
 */
class SeoContentSeeder extends Seeder
{
    /** Posts edited in the admin no longer carry this line and are left alone. */
    private const ORIGINAL_MARKER = 'Last checked against the official source on 2 November 2026';

    private const CHECKED = '<p><em>Last checked against the official source on 2 November 2026. Always confirm the eligibility, quota and last date in the official advertisement before applying. Never pay an agent to apply.</em></p>';

    private const NO_FEE_TIP = '<li>Apply only through the official portal linked on this page. Ignore anyone who asks for money to "confirm" your selection.</li>';

    public function run(): void
    {
        foreach ($this->articles() as $slug => $article) {
            $post = Post::where('slug', $slug)->first();

            if (! $post || ! str_contains($post->body, self::ORIGINAL_MARKER)) {
                $this->command?->warn("Skipped {$slug}");

                continue;
            }

            $post->update([
                'meta_title' => $article['meta_title'],
                'meta_description' => $article['meta_description'],
                'excerpt' => $article['excerpt'] ?? $post->excerpt,
                'tags' => $article['tags'],
                'faqs' => $article['faqs'],
                'body' => $article['body'].$this->related($article['related']).self::CHECKED,
                'featured_image' => $post->featured_image ?: ($article['image'] ?? null),
            ]);

            $this->command?->info("Updated {$slug}");
        }
    }

    /** @param array<string, string> $rows label => value */
    private function table(array $rows): string
    {
        $cells = collect($rows)->map(fn (string $value, string $label) => "<tr><td>{$label}</td><td>{$value}</td></tr>")->implode('');

        return "<table><tbody>{$cells}</tbody></table>";
    }

    /** @param array<string, string> $links label => site path */
    private function related(array $links): string
    {
        $items = collect($links)->map(fn (string $url, string $label) => "<li><a href=\"{$url}\">{$label}</a></li>")->implode('');

        return "<h2>Related Guides</h2><ul>{$items}</ul>";
    }

    private function njpSteps(string $post): string
    {
        return '<h2>How to Apply on the National Jobs Portal</h2><ol>'
            .'<li>Open the National Jobs Portal (njp.gov.pk) using the official link on this page.</li>'
            ."<li>Sign in, or create an account with your CNIC and a mobile number in your name.</li>"
            ."<li>Search for <strong>{$post}</strong> and open the advertisement.</li>"
            .'<li>Read the eligibility, domicile and quota rules carefully.</li>'
            .'<li>Complete your profile, upload the requested documents and submit before the last date.</li>'
            .'</ol>';
    }

    private function documents(): string
    {
        return '<h2>Documents to Keep Ready</h2><p>The advertisement lists the exact documents. Applicants are commonly asked for:</p><ul>'
            .'<li>CNIC</li><li>Domicile certificate</li><li>Educational certificates and transcripts</li>'
            .'<li>Experience certificates, where experience is required</li><li>A recent passport-size photograph</li></ul>';
    }

    private function articles(): array
    {
        return [
            'islamabad-police-dolphin-force-jobs-2026-executive-staff' => [
                'image' => 'posts/islamabad-police-dolphin.jpg',
                'meta_title' => 'Islamabad Police Dolphin Force Jobs 2026: Apply by 15 Nov',
                'meta_description' => 'Islamabad Police Dolphin Force jobs 2026 for Sub Inspector, ASI and Constable. Last date 15 November 2026. Apply online through NPF-TAS.',
                'tags' => ['Islamabad Police Jobs', 'Dolphin Force Jobs 2026', 'ICT Police', 'Police Jobs in Islamabad', 'Constable Jobs', 'NPF-TAS'],
                'faqs' => [
                    ['question' => 'What is the last date for Islamabad Police Dolphin Force jobs 2026?', 'answer' => 'The official NPF-TAS portal lists 15 November 2026 as the last date.'],
                    ['question' => 'Where do I apply for Dolphin Force jobs?', 'answer' => 'Applications are made online through the NPF-TAS job portal at npftas.pk.'],
                    ['question' => 'Which posts are included?', 'answer' => 'Published reports of the advertisement list Sub Inspector (BS-14), Assistant Sub Inspector (BS-11) and Constable (BS-07). Confirm the posts in the official advertisement.'],
                ],
                'related' => ['All Army & Police Jobs' => '/jobs/army-police-jobs', 'How to Prepare for a Job Interview' => '/blog/how-to-prepare-for-a-job-interview-in-pakistan', 'How to Write a CV for Jobs in Pakistan' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan'],
                'body' => '<p>Islamabad Capital Territory (ICT) Police has opened recruitment for <strong>Executive Staff in the Dolphin Force</strong>, its motorcycle patrol unit in Islamabad. The official NPF-TAS job portal shows the advertisement as published on 1 November 2026, with <strong>15 November 2026</strong> as the last date to apply.</p>'
                    .'<h2>Islamabad Police Jobs 2026 at a Glance</h2>'
                    .$this->table(['Organization' => 'Islamabad Capital Territory Police', 'Unit' => 'Dolphin Force (Executive Staff)', 'Reported posts' => 'Sub Inspector (BS-14), Assistant Sub Inspector (BS-11), Constable (BS-07)', 'Reported vacancies' => '1,058', 'Location' => 'Islamabad', 'Last date' => '15 November 2026', 'Apply through' => 'NPF-TAS (npftas.pk)'])
                    .'<p>The post names, grades and vacancy total above come from published reports of the advertisement. The NPF-TAS portal confirms the title and dates. Read the advertisement on the portal for age limits, education, physical standards, quotas and any test fee.</p>'
                    .'<h2>How to Apply for Dolphin Force Jobs</h2><ol><li>Open npftas.pk using the official link on this page.</li><li>Download and read the advertisement for your post.</li><li>Register on the portal and fill in the online form.</li><li>Upload the required documents and submit before 15 November 2026.</li><li>Save your application slip for the test and interview stages.</li></ol>'
                    .$this->documents()
                    .'<h2>Tips Before You Apply</h2><ul><li>Check the physical standards early, because they are tested separately.</li><li>Apply a few days before the deadline in case the portal is busy.</li>'.self::NO_FEE_TIP.'</ul>',
            ],
            'pakistan-army-soldier-recruitment-registration-open' => [
                'image' => 'posts/pak-army-soldier.jpg',
                'meta_title' => 'Pak Army Soldier Recruitment 2026: Register by 23 November',
                'meta_description' => 'Pakistan Army soldier recruitment registration is open until 23 November 2026 on Join Pak Army. See how to register and prepare.',
                'tags' => ['Pak Army Jobs', 'Soldier Recruitment 2026', 'Join Pak Army', 'Army Jobs in Pakistan', 'Sipahi Bharti'],
                'faqs' => [
                    ['question' => 'What is the last date for Pak Army soldier registration?', 'answer' => 'The Join Pak Army website shows registration open until 23 November 2026.'],
                    ['question' => 'Where do I register for soldier recruitment?', 'answer' => 'Register online at joinpakarmy.gov.pk under Recruitment as a Soldier.'],
                    ['question' => 'Are there medical standards?', 'answer' => 'Yes. Join Pak Army publishes separate medical and physical standards for soldier recruitment. Check them before you register.'],
                ],
                'related' => ['PMA 159 Long Course' => '/jobs/pma-159-long-course-join-pakistan-army-as-an-officer', 'All Army & Police Jobs' => '/jobs/army-police-jobs', 'Islamabad Police Dolphin Force Jobs' => '/jobs/islamabad-police-dolphin-force-jobs-2026-executive-staff'],
                'body' => '<p>Registration for <strong>Pakistan Army soldier recruitment</strong> is open on the official Join Pak Army website. Young men and women across Pakistan who want to serve as soldiers can register online until <strong>23 November 2026</strong>.</p>'
                    .'<h2>Pak Army Soldier Recruitment at a Glance</h2>'
                    .$this->table(['Organization' => 'Pakistan Army', 'Post' => 'Soldier (Sipahi)', 'Location' => 'All Pakistan', 'Registration closes' => '23 November 2026', 'Apply through' => 'joinpakarmy.gov.pk'])
                    .'<h2>How to Register</h2><ol><li>Open Join Pak Army using the official link on this page.</li><li>Select <strong>Recruitment as a Soldier</strong>.</li><li>Read the eligibility rules and the medical standards for soldiers.</li><li>Complete online registration before 23 November 2026.</li><li>Follow the website for test and exam slip updates.</li></ol>'
                    .'<h2>Prepare for Selection</h2><ul><li>Review the published medical and physical standards so there are no surprises.</li><li>Start basic fitness training such as running and push-ups early.</li><li>Keep your CNIC or B-Form, domicile and education certificates ready.</li>'.self::NO_FEE_TIP.'</ul>',
            ],
            'pma-159-long-course-join-pakistan-army-as-an-officer' => [
                'image' => 'posts/pma-159-long-course.jpg',
                'meta_title' => 'PMA 159 Long Course: Join Pakistan Army as an Officer',
                'meta_description' => 'PMA 159 Long Course applications are open on Join Pak Army. Learn how to apply to become a commissioned officer in the Pakistan Army.',
                'tags' => ['PMA Long Course', 'PMA 159', 'Join Pak Army as Officer', 'Army Commission', 'Pakistan Military Academy'],
                'faqs' => [
                    ['question' => 'Is PMA 159 Long Course open?', 'answer' => 'The Join Pak Army website lists PMA 159 Long Course (September 2026) as open for applications.'],
                    ['question' => 'What is the last date for PMA 159?', 'answer' => 'The Join Pak Army homepage does not show a closing date. Check the course page for the current deadline.'],
                    ['question' => 'Where is the training held?', 'answer' => 'Long Course cadets train at the Pakistan Military Academy (PMA) in Kakul, Abbottabad.'],
                ],
                'related' => ['Pak Army Soldier Recruitment' => '/jobs/pakistan-army-soldier-recruitment-registration-open', 'All Army & Police Jobs' => '/jobs/army-police-jobs', 'How to Prepare for a Job Interview' => '/blog/how-to-prepare-for-a-job-interview-in-pakistan'],
                'body' => '<p>The <strong>PMA 159 Long Course</strong> is the regular route to become a commissioned officer in the Pakistan Army. The official Join Pak Army website lists the September 2026 course as open for applications.</p>'
                    .'<h2>PMA 159 Long Course at a Glance</h2>'
                    .$this->table(['Organization' => 'Pakistan Army', 'Course' => 'PMA 159 Long Course (September 2026)', 'Training' => 'Pakistan Military Academy, Kakul (Abbottabad)', 'Status' => 'Open', 'Apply through' => 'joinpakarmy.gov.pk'])
                    .'<p>The homepage does not show a closing date, so check the course page on Join Pak Army before you apply.</p>'
                    .'<h2>How to Apply</h2><ol><li>Open Join Pak Army using the official link on this page.</li><li>Select <strong>PMA Long Course</strong> and read the eligibility and medical standards.</li><li>Submit your online application.</li><li>Print your exam slip when it is issued and prepare for the initial tests.</li></ol>'
                    .'<h2>Selection Stages</h2><p>Officer selection usually includes initial tests, a medical examination and the Inter Services Selection Board (ISSB). The course page sets out the exact stages and dates.</p>'
                    .'<h2>Preparation Tips</h2><ul><li>Practise verbal and non-verbal intelligence tests.</li><li>Build physical fitness and follow current affairs.</li>'.self::NO_FEE_TIP.'</ul>',
            ],
            'junior-auditor-jobs-bps-11-controller-general-of-accounts-balochistan' => [
                'image' => 'posts/cga-junior-auditor.jpg',
                'meta_title' => 'Junior Auditor Jobs BPS-11 Balochistan: 39 Posts, Apply by 26 Nov',
                'meta_description' => 'Controller General of Accounts Junior Auditor jobs (BPS-11), 39 vacancies listed under Balochistan. Last date 26 November 2026. Apply on NJP.',
                'tags' => ['Junior Auditor Jobs', 'CGA Jobs', 'Balochistan Jobs 2026', 'BPS-11 Jobs', 'Quetta Jobs', 'National Jobs Portal'],
                'faqs' => [
                    ['question' => 'How many Junior Auditor posts are there?', 'answer' => 'The National Jobs Portal lists 39 vacancies for Junior Auditor (BPS-11).'],
                    ['question' => 'What is the last date to apply?', 'answer' => 'The portal lists 26 November 2026 as the last date.'],
                    ['question' => 'Who can apply?', 'answer' => 'The post is listed under Balochistan on the portal. Check the domicile and quota rules in the advertisement before applying.'],
                ],
                'related' => ['All Government Jobs' => '/jobs/government-jobs', 'Balochistan jobs on our Jobs page' => '/jobs?location=Balochistan', 'How to Write a CV for Jobs in Pakistan' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan'],
                'body' => '<p>The <strong>Office of the Controller General of Accounts (CGA)</strong> has listed <strong>39 Junior Auditor (BPS-11)</strong> posts on the National Jobs Portal. The posts are listed under Balochistan and close on <strong>26 November 2026</strong>, making this one of the larger federal openings for candidates from Quetta and other districts of Balochistan.</p>'
                    .'<h2>Junior Auditor Jobs at a Glance</h2>'
                    .$this->table(['Organization' => 'Office of the Controller General of Accounts', 'Post' => 'Junior Auditor', 'Pay scale' => 'BPS-11', 'Vacancies' => '39', 'Job type' => 'Regular (permanent)', 'Listed under' => 'Balochistan', 'Last date' => '26 November 2026'])
                    .'<p>The listing places these posts under Balochistan, which usually means a Balochistan domicile is required. Confirm this in the advertisement.</p>'
                    .$this->njpSteps('Junior Auditor')
                    .$this->documents()
                    .'<h2>Tips</h2><ul><li>Revise basic accounting, arithmetic and English for the written test.</li><li>Use the same name and dates as on your CNIC in every form.</li>'.self::NO_FEE_TIP.'</ul>',
            ],
            'financial-analyst-gas-petroleum-division' => [
                'image' => 'posts/petroleum-financial-analyst.jpg',
                'meta_title' => 'Financial Analyst (Gas) Job, Petroleum Division: Apply by 8 Nov',
                'meta_description' => 'Petroleum Division Financial Analyst (Gas) contract job on SPPS-IV. 3 or more years of experience. Last date 8 November 2026 on NJP.',
                'tags' => ['Petroleum Division Jobs', 'Financial Analyst Jobs', 'Ministry of Energy Jobs', 'Contract Jobs Pakistan', 'Finance Jobs'],
                'faqs' => [
                    ['question' => 'What is the last date for the Financial Analyst (Gas) job?', 'answer' => 'The National Jobs Portal lists 8 November 2026 as the last date.'],
                    ['question' => 'Is this a permanent job?', 'answer' => 'No. The portal lists it as a contract post on the SPPS-IV scale.'],
                    ['question' => 'How much experience is needed?', 'answer' => 'The listing asks for 3 years or more of experience.'],
                ],
                'related' => ['All Government Jobs' => '/jobs/government-jobs', 'Contract jobs on our Jobs page' => '/jobs?type[]=Contract', 'How to Prepare for a Job Interview' => '/blog/how-to-prepare-for-a-job-interview-in-pakistan'],
                'body' => '<p>The <strong>Petroleum Division (Ministry of Energy)</strong> is hiring a <strong>Financial Analyst (Gas)</strong> on contract. The post is listed on the National Jobs Portal on the SPPS-IV scale, and applications close on <strong>8 November 2026</strong>.</p>'
                    .'<h2>Job at a Glance</h2>'
                    .$this->table(['Organization' => 'Petroleum Division, Ministry of Energy', 'Post' => 'Financial Analyst (Gas)', 'Pay scale' => 'SPPS-IV', 'Vacancies' => '1', 'Job type' => 'Contract', 'Experience' => '3 years or more', 'Last date' => '8 November 2026'])
                    .'<p>This role suits finance professionals with experience in the gas or energy sector. Read the advertisement for the exact qualification and experience rules.</p>'
                    .$this->njpSteps('Financial Analyst (Gas)')
                    .$this->documents()
                    .'<h2>Tips</h2><ul><li>Highlight financial modelling and energy sector work in your CV.</li><li>The deadline is close, so apply early.</li>'.self::NO_FEE_TIP.'</ul>',
            ],
            'junior-accountant-bps-11-cadet-college-hasanabdal' => [
                'image' => 'posts/cadet-college-junior-accountant.jpg',
                'meta_title' => 'Junior Accountant Job BPS-11, Cadet College Hasanabdal',
                'meta_description' => 'Cadet College Hasanabdal Junior Accountant (BPS-11) contract job. 2 or more years of experience. Last date 15 November 2026 on NJP.',
                'tags' => ['Cadet College Hasanabdal Jobs', 'Junior Accountant Jobs', 'Hasan Abdal Jobs', 'Attock Jobs', 'BPS-11 Jobs'],
                'faqs' => [
                    ['question' => 'What is the last date for the Junior Accountant job?', 'answer' => 'The National Jobs Portal lists 15 November 2026 as the last date.'],
                    ['question' => 'Where is the job?', 'answer' => 'The post is at Cadet College Hasanabdal in Hasan Abdal, Attock district, Punjab.'],
                    ['question' => 'How much experience is required?', 'answer' => 'The listing asks for 2 years or more of experience.'],
                ],
                'related' => ['All Teaching & Education Jobs' => '/jobs/teaching-jobs', 'How to Write a CV for Jobs in Pakistan' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan', 'Career Advice' => '/blog'],
                'body' => '<p><strong>Cadet College Hasanabdal</strong>, one of Pakistan\'s best-known cadet colleges in Hasan Abdal (Attock district, Punjab), has listed a <strong>Junior Accountant (BPS-11)</strong> post on the National Jobs Portal. It is a contract post and applications close on <strong>15 November 2026</strong>.</p>'
                    .'<h2>Job at a Glance</h2>'
                    .$this->table(['Organization' => 'Cadet College Hasanabdal', 'Post' => 'Junior Accountant', 'Pay scale' => 'BPS-11', 'Vacancies' => '1', 'Job type' => 'Contract', 'Experience' => '2 years or more', 'Location' => 'Hasan Abdal, Attock (Punjab)', 'Last date' => '15 November 2026'])
                    .$this->njpSteps('Junior Accountant')
                    .$this->documents()
                    .'<h2>Tips</h2><ul><li>Show bookkeeping, payroll and accounting software experience in your CV.</li><li>Candidates from Attock, Rawalpindi and nearby districts should note the job location before applying.</li>'.self::NO_FEE_TIP.'</ul>',
            ],
            'senior-officer-finance-accounts-exim-bank-of-pakistan' => [
                'image' => 'posts/exim-senior-officer.jpg',
                'meta_title' => 'EXIM Bank Senior Officer Finance & Accounts Job, Islamabad',
                'meta_description' => 'EXIM Bank of Pakistan Senior Officer Finance & Accounts (M6) job in Islamabad. 3 or more years of experience. Last date 4 November 2026.',
                'tags' => ['EXIM Bank Jobs', 'Bank Jobs in Islamabad', 'Finance Jobs', 'Bank Jobs 2026', 'Accounts Jobs'],
                'faqs' => [
                    ['question' => 'What is the last date for the EXIM Bank job?', 'answer' => 'The National Jobs Portal lists 4 November 2026 as the last date.'],
                    ['question' => 'Where is the job based?', 'answer' => 'The post is in Islamabad.'],
                    ['question' => 'How much experience is required?', 'answer' => 'The listing asks for 3 years or more of experience.'],
                ],
                'related' => ['All Bank Jobs' => '/jobs/bank-jobs', 'Bank Schemes & Loans' => '/government-schemes/bank-schemes-loans', 'How to Prepare for a Job Interview' => '/blog/how-to-prepare-for-a-job-interview-in-pakistan'],
                'body' => '<p><strong>EXIM Bank of Pakistan</strong>, the country\'s export-import bank, has listed a <strong>Senior Officer Finance &amp; Accounts</strong> post in Islamabad on the National Jobs Portal. It is a regular post on grade M6, and applications close on <strong>4 November 2026</strong>.</p>'
                    .'<h2>Job at a Glance</h2>'
                    .$this->table(['Organization' => 'EXIM Bank of Pakistan', 'Post' => 'Senior Officer Finance & Accounts', 'Grade' => 'M6', 'Vacancies' => '1', 'Job type' => 'Regular', 'Experience' => '3 years or more', 'Location' => 'Islamabad', 'Last date' => '4 November 2026'])
                    .$this->njpSteps('Senior Officer Finance & Accounts')
                    .$this->documents()
                    .'<h2>Tips</h2><ul><li>Banks look for accounting qualifications such as ACCA, CA or a finance degree. Check the advertisement for the exact requirement.</li><li>The deadline is very close. Apply today if you are eligible.</li>'.self::NO_FEE_TIP.'</ul>',
            ],
            'pm-youth-chromebook-scheme-eligibility-and-how-to-apply' => [
                'image' => 'posts/pm-chromebook.jpg',
                'meta_title' => 'PM Youth Chromebook Scheme 2026: Eligibility & How to Apply',
                'meta_description' => 'PM Youth Chromebook Scheme 2026 for top Grade 9 government school students. Check eligibility, documents and how to apply on the IBCC portal.',
                'tags' => ['PM Chromebook Scheme', 'PM Youth Programme', 'IBCC Chromebook', 'Free Chromebook for Students', 'Student Scholarships'],
                'faqs' => [
                    ['question' => 'Who can apply for the PM Youth Chromebook Scheme?', 'answer' => 'Top-performing Grade 9 students of government schools, based on the Annual Examinations 2026. For AJK, Islamabad and Gilgit-Baltistan boards, Grade 10 students are also considered.'],
                    ['question' => 'Where do I apply?', 'answer' => 'Apply on the official IBCC portal at pmychromebooks.ibcc.edu.pk.'],
                    ['question' => 'Can Sindh students apply?', 'answer' => 'Sindh registration opens board by board after Class 9 results. The portal showed BISE Larkano open and other Sindh boards on hold or coming soon.'],
                ],
                'related' => ['All Student Scholarships' => '/government-schemes/student-scholarships', 'CM Punjab Laptop Program Phase 2' => '/government-schemes/cm-punjab-laptop-program-phase-2-eligibility-and-latest-status', 'Career Advice' => '/blog'],
                'body' => '<p>The <strong>Prime Minister\'s Youth Chromebook Scheme</strong> gives Chromebooks to top-performing government school students across Pakistan. Applications are made on the official <strong>IBCC portal</strong>, based on the Annual Examinations 2026.</p>'
                    .'<h2>Scheme at a Glance</h2>'
                    .$this->table(['Scheme' => 'Prime Minister\'s Youth Chromebook Scheme', 'For' => 'Top-performing government school students', 'Classes' => 'Grade 9 (SSC-I); also Grade 10 for AJK, ICT and GB boards', 'Status' => 'Open (Sindh boards opening in phases)', 'Apply through' => 'pmychromebooks.ibcc.edu.pk'])
                    .'<h2>Who Can Apply</h2><ul><li>Punjab, Sindh, Khyber Pakhtunkhwa and Balochistan boards: top-performing Grade 9 students.</li><li>AJK, Islamabad and Gilgit-Baltistan boards: top-performing Grade 9 and Grade 10 students.</li><li>The portal states that only students enrolled in government schools are eligible.</li></ul>'
                    .'<h2>Documents Needed</h2><ul><li>Recent colour photograph with a plain background (JPG or PNG, up to 300 KB).</li><li>SSC-I or SSC-II mark sheet.</li><li>Attested copy of your B-Form or CNIC.</li><li>Attested copy of your father\'s or guardian\'s CNIC.</li></ul>'
                    .'<h2>How to Apply</h2><ol><li>Open the IBCC portal and select your board.</li><li>Enter your CNIC or B-Form, roll or registration number and date of birth.</li><li>Check your details and upload your documents.</li><li>Submit and save your reference number and slip.</li></ol>'
                    .'<p>Each student can apply once, and the form cannot be changed after submission.</p>',
            ],
            'apni-chhat-apna-ghar-interest-free-home-construction-loan-in-punjab' => [
                'image' => 'posts/apni-chhat-apna-ghar.jpg',
                'meta_title' => 'Apni Chhat Apna Ghar 2026: Interest-Free Home Loan in Punjab',
                'meta_description' => 'Apni Chhat Apna Ghar offers interest-free loans to build a home in Punjab. Check eligibility, plot size rules and how to apply on acag.punjab.gov.pk.',
                'tags' => ['Apni Chhat Apna Ghar', 'Interest Free Loan Punjab', 'Housing Scheme Punjab', 'ACAG Portal', 'PHATA'],
                'faqs' => [
                    ['question' => 'Who is eligible for Apni Chhat Apna Ghar?', 'answer' => 'Punjab residents aged 21 to 60 with a CNIC who solely own a plot of up to 5 marla in urban areas or 10 marla in rural areas, with no criminal record and no loan default.'],
                    ['question' => 'How do I apply?', 'answer' => 'Apply online at acag.punjab.gov.pk by selecting Apply for Construction of New House.'],
                    ['question' => 'Is there a helpline?', 'answer' => 'Yes. The toll-free helpline is 0800-09100.'],
                ],
                'related' => ['Apni Chhat Mehfooz Chhat' => '/government-schemes/apni-chhat-mehfooz-chhat-interest-free-loan-for-roof-repair-or-an-extra-room', 'All Housing Schemes' => '/government-schemes/housing-schemes', 'Bank Schemes & Loans' => '/government-schemes/bank-schemes-loans'],
                'body' => '<p><strong>Apni Chhat Apna Ghar</strong> is a Government of Punjab programme that gives interest-free loans to low-income families to build their own home. The official portal reports more than 228,000 loans disbursed across Punjab.</p>'
                    .'<h2>Scheme at a Glance</h2>'
                    .$this->table(['Scheme' => 'Apni Chhat Apna Ghar', 'Run by' => 'Government of Punjab (PHATA)', 'Loan' => 'Interest-free, up to Rs. 1.5 million (reported), repaid over 9 years (reported)', 'For' => 'Building a new house', 'Status' => 'Open', 'Apply through' => 'acag.punjab.gov.pk', 'Helpline' => '0800-09100'])
                    .'<p>The loan amount and repayment period come from news reports. Confirm them on the portal or the helpline.</p>'
                    .'<h2>Who Can Apply</h2><ul><li>Punjab resident with a CNIC, aged 21 to 60 at the time of applying.</li><li>Sole owner and possessor of a plot up to 5 marla in urban areas or 10 marla in rural areas.</li><li>No criminal record, and not a loan defaulter.</li></ul>'
                    .'<h2>How to Apply</h2><ol><li>Open acag.punjab.gov.pk.</li><li>Select <strong>Apply for Construction of New House</strong>.</li><li>Fill in the form and follow the instructions.</li></ol>'
                    .'<h2>Early Repayment Rebate</h2><p>The portal offers a rebate on the outstanding principal for full early repayment: 20% in years 1 to 3, and 10% in years 4 to 6.</p>',
            ],
            'apni-chhat-mehfooz-chhat-interest-free-loan-for-roof-repair-or-an-extra-room' => [
                'image' => 'posts/apni-chhat-mehfooz-chhat.jpg',
                'meta_title' => 'Apni Chhat Mehfooz Chhat: Interest-Free Roof Repair Loan',
                'meta_description' => 'Apni Chhat Mehfooz Chhat gives Punjab homeowners interest-free loans for roof repair or an extra room. Check eligibility and apply on acag.punjab.gov.pk.',
                'tags' => ['Apni Chhat Mehfooz Chhat', 'Roof Repair Loan', 'Interest Free Loan Punjab', 'Housing Scheme Punjab', 'ACAG Portal'],
                'faqs' => [
                    ['question' => 'What can the Mehfooz Chhat loan be used for?', 'answer' => 'Reported limits are up to Rs. 500,000 to repair or secure a roof, and up to Rs. 1 million for an extra room or second storey.'],
                    ['question' => 'Do I need a PMT score?', 'answer' => 'Yes. Applicants must be registered with NSER or PSER with a PMT score of 60 or less.'],
                    ['question' => 'Where do I apply?', 'answer' => 'Apply at acag.punjab.gov.pk under Apply for Addition of New Floor/Room, or call 0800-09100.'],
                ],
                'related' => ['Apni Chhat Apna Ghar' => '/government-schemes/apni-chhat-apna-ghar-interest-free-home-construction-loan-in-punjab', 'All Housing Schemes' => '/government-schemes/housing-schemes', 'Bank Schemes & Loans' => '/government-schemes/bank-schemes-loans'],
                'body' => '<p><strong>Apni Chhat Mehfooz Chhat</strong> helps low-income homeowners in Punjab make their houses safer or larger with an interest-free loan. It was launched in July 2026 under the Apni Chhat Apna Ghar programme.</p>'
                    .'<h2>Scheme at a Glance</h2>'
                    .$this->table(['Scheme' => 'Apni Chhat Mehfooz Chhat', 'Run by' => 'Government of Punjab (PHATA)', 'Roof repair loan' => 'Up to Rs. 500,000 (reported)', 'Extra room or storey' => 'Up to Rs. 1 million (reported)', 'Monthly instalment' => 'About Rs. 4,700 to Rs. 9,300 (reported)', 'Status' => 'Open', 'Apply through' => 'acag.punjab.gov.pk'])
                    .'<p>Loan limits and instalments come from news reports. Confirm them on the portal or the helpline 0800-09100.</p>'
                    .'<h2>Who Can Apply</h2><ul><li>Punjab resident with a CNIC, aged 21 to 60.</li><li>Registered with NSER or PSER with a PMT score of 60 or less.</li><li>Owner of a constructed house up to 5 marla (urban) or 10 marla (rural).</li><li>No criminal record, and not a loan defaulter.</li></ul>'
                    .'<h2>How to Apply</h2><ol><li>Open acag.punjab.gov.pk.</li><li>Select <strong>Apply for Addition of New Floor/Room</strong>.</li><li>Complete the form, or apply at your Deputy Commissioner, Assistant Commissioner or PHATA office.</li></ol>',
            ],
            'pave-electric-bike-and-rickshaw-program-subsidy-and-eligibility' => [
                'image' => 'posts/pave-e-bike.jpg',
                'meta_title' => 'PAVE Electric Bike & Rickshaw Program: Subsidy & Eligibility',
                'meta_description' => 'PAVE program subsidy on electric bikes, rickshaws and loaders across Pakistan. See subsidy amounts, age limits and how to apply on pave.gov.pk.',
                'tags' => ['PAVE Scheme', 'Electric Bike Scheme Pakistan', 'E-Bike Subsidy', 'Electric Rickshaw Loan', 'Ministry of Industries'],
                'faqs' => [
                    ['question' => 'Who can apply for the PAVE program?', 'answer' => 'Pakistani citizens including AJK and Gilgit-Baltistan. E-bikes: ages 18 to 65. Rickshaws and loaders: ages 21 to 65.'],
                    ['question' => 'How much is the subsidy?', 'answer' => 'Under bank lease the portal lists Rs. 50,000 on a bike and Rs. 200,000 on a rickshaw or loader. Under self finance it lists Rs. 80,000 and Rs. 400,000.'],
                    ['question' => 'Is there a helpline?', 'answer' => 'Yes. Call 1048 during working hours.'],
                ],
                'related' => ['CM Punjab E-Bike Scheme Phase 2' => '/government-schemes/cm-punjab-e-bike-scheme-phase-2-for-students-terms-and-status', 'All Financial Assistance' => '/government-schemes/government-financial-assistance', 'Bank Schemes & Loans' => '/government-schemes/bank-schemes-loans'],
                'body' => '<p>The <strong>Pakistan Accelerated Vehicle Electrification (PAVE) Program</strong>, run under the Ministry of Industries and Production, offers subsidies on electric bikes, rickshaws and loaders for citizens across Pakistan, including AJK and Gilgit-Baltistan.</p>'
                    .'<h2>Program at a Glance</h2>'
                    .$this->table(['Program' => 'PAVE', 'Run by' => 'Ministry of Industries & Production', 'Electric bikes' => '116,000, with a 25% quota for women', 'Rickshaws and loaders' => '3,170', 'Bank lease subsidy' => 'Rs. 50,000 (bike), Rs. 200,000 (rickshaw or loader)', 'Self finance subsidy' => 'Rs. 80,000 (bike), Rs. 400,000 (rickshaw or loader)', 'Helpline' => '1048'])
                    .'<h2>Who Can Apply</h2><ul><li>Electric bikes: ages 18 to 65.</li><li>Electric rickshaws and loaders: ages 21 to 65.</li></ul>'
                    .'<h2>Two Ways to Buy</h2><ul><li><strong>Bank lease:</strong> the bank verifies you, pays the manufacturer and recovers the balance in instalments.</li><li><strong>Self finance:</strong> you pay the manufacturer, upload your documents, and the subsidy is paid to your bank account after verification.</li></ul>'
                    .'<h2>Before You Apply</h2><p>The portal does not show a clear closing date. Check on pave.gov.pk that applications are open for your category, or call 1048.</p>',
            ],
            'cm-punjab-e-bike-scheme-phase-2-for-students-terms-and-status' => [
                'image' => 'posts/cm-punjab-e-bike-phase-2.jpg',
                'meta_title' => 'CM Punjab E-Bike Scheme Phase 2 for Students: Terms & Status',
                'meta_description' => 'CM Punjab E-Bike Scheme Phase 2 offered 100,000 student scooties with a Rs. 90,000 subsidy. Applications closed on 4 October 2026. See the terms.',
                'tags' => ['CM Punjab E-Bike Scheme', 'E-Bike Scheme Phase 2', 'Student Scooty Scheme', 'Punjab Students', 'Electric Scooty'],
                'faqs' => [
                    ['question' => 'Is the CM Punjab E-Bike Scheme Phase 2 still open?', 'answer' => 'No. The official portal listed 4 October 2026 as the last date to apply.'],
                    ['question' => 'What did the scheme offer?', 'answer' => 'An electric scooty priced at Rs. 199,000 with a Rs. 90,000 government subsidy, 0% interest, no down payment and a three-year financing period.'],
                    ['question' => 'Is there another electric bike scheme?', 'answer' => 'The federal PAVE program offers subsidies on electric bikes. Check pave.gov.pk for its current status.'],
                ],
                'related' => ['PAVE Electric Bike Program' => '/government-schemes/pave-electric-bike-and-rickshaw-program-subsidy-and-eligibility', 'All Financial Assistance' => '/government-schemes/government-financial-assistance', 'Student Scholarships' => '/government-schemes/student-scholarships'],
                'body' => '<p>Phase 2 of the <strong>CM Punjab E-Bike Scheme</strong> offered 100,000 electric scooties to students across Punjab. <strong>Applications closed on 4 October 2026</strong>, according to the official portal. This page keeps the terms for reference.</p>'
                    .'<h2>Scheme at a Glance</h2>'
                    .$this->table(['Scheme' => 'CM Punjab E-Bike Scheme, Phase 2', 'For' => 'Students in Punjab', 'Scooties' => '100,000', 'Price' => 'Rs. 199,000', 'Government subsidy' => 'Rs. 90,000', 'Financing' => '0% interest, no down payment, 3 years', 'Monthly instalment' => 'About Rs. 3,000', 'Status' => 'Closed (last date 4 October 2026)'])
                    .'<h2>Who Was Eligible</h2><ul><li>Bonafide students aged 16 or older at institutions recognised by the Government of Punjab. Federally chartered institutes were not eligible.</li><li>A valid driving licence, learner\'s permit or juvenile permit.</li><li>A guarantor with a verifiable monthly income of at least Rs. 40,000, a recent bank statement and a SIM in their own name.</li></ul>'
                    .'<h2>What Was Included</h2><ul><li>Insurance, registration and token tax paid by the Government of Punjab.</li><li>A free helmet and safety rods.</li></ul>'
                    .'<p>Watch this page for any new phase. Until then, students can look at the federal PAVE program.</p>',
            ],
        ];
    }
}
