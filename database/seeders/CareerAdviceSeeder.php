<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\Concerns\SeedsImages;
use Illuminate\Database\Seeder;

/**
 * Career Advice articles. Rewrites the four sample blogs while they still hold sample text,
 * and adds new guides once. Run on its own: php artisan db:seed --class=CareerAdviceSeeder --force
 */
class CareerAdviceSeeder extends Seeder
{
    use SeedsImages;

    private const SAMPLE_MARKER = 'Sample content: add the final text';

    public function run(): void
    {
        foreach ($this->articles() as $index => $article) {
            $post = BlogPost::where('slug', $article['slug'])->first();

            if ($post && ! str_contains($post->body, self::SAMPLE_MARKER)) {
                $post->update(['topic' => $post->topic ?? $article['topic']]);
                $this->command?->warn("Kept {$article['slug']}");

                continue;
            }

            $values = [
                'author_id' => User::where('email', $article['author'])->value('id') ?? User::value('id'),
                'category_id' => Category::where('slug', $article['category'])->value('id'),
                'topic' => $article['topic'],
                'title' => $article['title'],
                'excerpt' => $article['excerpt'],
                'body' => $article['body'].$this->related($article['related']),
                'meta_title' => $article['meta_title'],
                'meta_description' => $article['meta_description'],
                'tags' => $article['tags'],
                'faqs' => $article['faqs'],
                'is_featured' => $article['featured'] ?? false,
                'status' => 'published',
            ];

            if ($post) {
                $post->update($values + ['featured_image' => $post->featured_image ?: $this->seedImage($article['image'])]);
            } else {
                BlogPost::create($values + [
                    'slug' => $article['slug'],
                    'featured_image' => $this->seedImage($article['image']),
                    'published_at' => now()->subDays($index + 1),
                ]);
            }

            $this->command?->info("Wrote {$article['slug']}");
        }
    }

    /** @param array<string, string> $links label => site path */
    private function related(array $links): string
    {
        $items = collect($links)->map(fn (string $url, string $label) => "<li><a href=\"{$url}\">{$label}</a></li>")->implode('');

        return "<h2>Keep Reading</h2><ul>{$items}</ul>";
    }

    private function articles(): array
    {
        return [
            [
                'slug' => 'how-to-write-a-cv-tips-for-jobs-in-pakistan',
                'title' => 'How to Write a CV: Tips for Jobs in Pakistan',
                'topic' => 'cv-resume', 'category' => 'jobs', 'author' => 'sara.author@example.com', 'image' => 'jobs-illustration', 'featured' => true,
                'excerpt' => 'A simple, step-by-step way to build a clear CV that gets read, whether you are applying for a government post or a private job.',
                'meta_title' => 'How to Write a CV for Jobs in Pakistan: Simple Guide',
                'meta_description' => 'Learn how to write a clear, professional CV for government and private jobs in Pakistan, with a simple layout, examples of what to include and common mistakes.',
                'tags' => ['CV Writing', 'Resume Tips', 'CV Format Pakistan', 'Fresh Graduate CV', 'Job Application'],
                'faqs' => [
                    ['question' => 'How long should a CV be in Pakistan?', 'answer' => 'One page is usually enough for fresh graduates. Two pages are fine once you have several years of relevant experience.'],
                    ['question' => 'Should I add a photo to my CV?', 'answer' => 'Many employers in Pakistan still expect one. Use a recent, plain-background photo, and leave it out if the job advert asks you to.'],
                    ['question' => 'Do I need a different CV for every job?', 'answer' => 'Keep one master CV, then adjust the summary and skills for each job so they match the advert.'],
                ],
                'related' => ['How to Write a Cover Letter' => '/blog/how-to-write-a-cover-letter-for-jobs-in-pakistan', 'Common Interview Questions' => '/blog/common-job-interview-questions-and-how-to-answer-them', 'Browse the latest jobs' => '/jobs'],
                'body' => <<<'HTML'
                    <p>Your CV is usually the first thing an employer sees, and most recruiters spend less than a minute on it before deciding whether to read on. The good news is that a strong CV does not need fancy design. It needs to be clear, honest and easy to scan.</p>
                    <h2>Start With the Right Layout</h2>
                    <p>Keep the layout simple: one clean font, clear headings and plenty of white space. A single page works well for fresh graduates, and two pages are fine once you have real experience to show.</p>
                    <ol><li><strong>Contact details:</strong> full name, phone number, a professional email address and your city.</li><li><strong>Profile summary:</strong> three or four lines about who you are and the role you want.</li><li><strong>Education:</strong> degree, institution and year, with your latest qualification first.</li><li><strong>Experience:</strong> job title, organisation, dates and two or three achievements for each role.</li><li><strong>Skills:</strong> the ones that match the job, such as MS Excel, accounting software or customer service.</li><li><strong>References:</strong> "Available on request" is enough unless the advert asks for names.</li></ol>
                    <h2>Write Achievements, Not Duties</h2>
                    <p>"Responsible for data entry" tells an employer very little. "Entered and checked 300 records a day with no errors" tells them you are reliable. Wherever you can, show what you achieved and how well you did it.</p>
                    <h2>Match Your CV to the Job</h2>
                    <p>Read the advert carefully and note the words it uses for skills and qualifications. If you have those skills, use the same words in your summary and skills section. This helps a busy recruiter, and it helps when applications are screened by software.</p>
                    <h2>Mistakes to Avoid</h2>
                    <ul><li>Spelling mistakes, which look careless. Ask a friend to proofread.</li><li>Personal details that are not needed, such as your CNIC number, religion or family details, unless the employer asks.</li><li>An unprofessional email address. Create a simple one with your name.</li><li>Gaps you do not explain. A short line such as "Preparing for competitive exams" is better than silence.</li></ul>
                    <h2>Final Check Before You Send</h2>
                    <p>Save your CV as a PDF so the formatting does not break, and name the file clearly, for example <em>Ali-Khan-CV.pdf</em>. Then read it once more as if you were the employer. If something does not help you get this job, remove it.</p>
                    HTML,
            ],
            [
                'slug' => 'how-to-prepare-for-a-job-interview-in-pakistan',
                'title' => 'How to Prepare for a Job Interview in Pakistan',
                'topic' => 'interview', 'category' => 'jobs', 'author' => 'sara.author@example.com', 'image' => 'jobs-healthcare', 'featured' => true,
                'excerpt' => 'Simple steps to research the employer, practise your answers and walk into the interview room with confidence.',
                'meta_title' => 'How to Prepare for a Job Interview in Pakistan',
                'meta_description' => 'Practical steps to prepare for a job interview in Pakistan: research the employer, practise common questions, plan your documents and make a good first impression.',
                'tags' => ['Interview Preparation', 'Job Interview Tips', 'Panel Interview', 'Government Job Interview', 'First Job'],
                'faqs' => [
                    ['question' => 'What should I wear to a job interview?', 'answer' => 'Clean, ironed formal or smart traditional clothes. Shalwar kameez with a waistcoat or a simple suit are both fine for most interviews in Pakistan.'],
                    ['question' => 'What documents should I take?', 'answer' => 'Your CNIC, call letter or interview slip, original certificates with photocopies, a few printed CVs and passport-size photos.'],
                    ['question' => 'How early should I arrive?', 'answer' => 'Aim to arrive 15 to 20 minutes early so you have time for security checks and to settle your nerves.'],
                ],
                'related' => ['Common Interview Questions and Answers' => '/blog/common-job-interview-questions-and-how-to-answer-them', 'How to Write a CV' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan', 'Army & Police Jobs' => '/jobs/army-police-jobs'],
                'body' => <<<'HTML'
                    <p>Getting an interview call means your application did its job. Now the employer wants to see whether you can do the work and whether you will fit in. With a little preparation, most of the nerves go away.</p>
                    <h2>Research the Organisation</h2>
                    <p>Spend half an hour on the organisation's website and the job advert. Know what they do, where they work and what the role involves. For a government post, read the department's role and the duties listed in the advertisement. Interviewers notice when a candidate has made this effort.</p>
                    <h2>Practise the Common Questions</h2>
                    <p>Most interviews include a few predictable questions: tell us about yourself, why do you want this job, what are your strengths, and where do you see yourself in a few years. Prepare short, honest answers and practise them aloud with a friend or in front of a mirror. Do not memorise word for word, or you will sound rehearsed.</p>
                    <h2>Prepare for Panel Interviews</h2>
                    <p>Many government and semi-government interviews are held in front of a panel. Make eye contact with the person who asked the question, then include the others as you answer. If you do not know something, say so politely instead of guessing.</p>
                    <h2>Get Your Documents Ready the Night Before</h2>
                    <ul><li>CNIC and your call letter or interview slip</li><li>Original certificates and a set of photocopies</li><li>Two or three printed copies of your CV</li><li>Passport-size photographs</li><li>A pen and a small notebook</li></ul>
                    <h2>On the Day</h2>
                    <p>Dress neatly, reach early and switch your phone to silent. Greet everyone politely, from the guard at the gate to the panel. Sit straight, listen to the full question and take a second to think before answering.</p>
                    <h2>After the Interview</h2>
                    <p>Note down the questions you were asked while they are fresh. Whatever the result, those notes will make your next interview easier.</p>
                    HTML,
            ],
            [
                'slug' => '7-things-to-check-before-applying-for-a-government-job',
                'title' => '7 Things to Check Before Applying for a Government Job',
                'topic' => 'job-application', 'category' => 'jobs', 'author' => 'admin.author@example.com', 'image' => 'jobs-banner', 'featured' => true,
                'excerpt' => 'Documents, eligibility and the common mistakes that get government job applications rejected, and how to avoid them.',
                'meta_title' => '7 Things to Check Before Applying for a Government Job',
                'meta_description' => 'Before applying for a government job in Pakistan, check these 7 things: eligibility, domicile, age limit, documents, fee, last date and the official portal.',
                'tags' => ['Government Jobs', 'Job Application Tips', 'Domicile', 'Age Relaxation', 'NJP'],
                'faqs' => [
                    ['question' => 'Why are government job applications rejected?', 'answer' => 'Common reasons are applying without the required qualification or domicile, missing documents, being over the age limit, and paying the fee incorrectly.'],
                    ['question' => 'Is domicile required for federal jobs?', 'answer' => 'Many federal posts are filled by provincial and regional quotas, so your domicile decides which seats you can apply for. The advertisement explains the quota.'],
                    ['question' => 'How do I know an advertisement is real?', 'answer' => 'Find it on the official website or a trusted official portal such as the National Jobs Portal. Be careful with adverts shared only on social media.'],
                ],
                'related' => ['How to Fill a Government Job Application Form' => '/blog/how-to-fill-a-government-job-application-form-correctly', 'All Government Jobs' => '/jobs/government-jobs', 'How to Write a CV' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan'],
                'body' => <<<'HTML'
                    <p>Every year many good candidates lose a chance at a government job for small, avoidable reasons. Before you spend money on fees and time on forms, run through these seven checks.</p>
                    <h2>1. Is the Advertisement Real?</h2>
                    <p>Find the advert on the department's official website or a trusted official portal such as the National Jobs Portal. Fake adverts often ask for fees through personal accounts or promise jobs without tests.</p>
                    <h2>2. Do You Meet the Qualification?</h2>
                    <p>Check the exact degree, subject and division the post asks for. A related degree is not always accepted. If the advert asks for experience, make sure it is the right kind and you can prove it.</p>
                    <h2>3. Does Your Domicile Fit the Quota?</h2>
                    <p>Many posts are divided by province or region. Your domicile decides which seats you can apply for, so read the quota table before you start.</p>
                    <h2>4. Are You Within the Age Limit?</h2>
                    <p>Work out your age on the closing date, not today. Read the age relaxation rules carefully, since they often apply to certain groups and are not automatic for everyone.</p>
                    <h2>5. Are Your Documents Ready?</h2>
                    <p>Keep your CNIC, domicile, degrees, transcripts, experience letters and photographs scanned and ready. Make sure names and dates match across every document.</p>
                    <h2>6. How Is the Fee Paid?</h2>
                    <p>Pay only by the method the advert describes, usually a bank challan or an online payment. Keep the receipt safe until the whole process is over.</p>
                    <h2>7. When Is the Last Date?</h2>
                    <p>Portals slow down near the deadline. Aim to apply at least a few days early, and save or print your application slip once you submit.</p>
                    <p>None of this takes long, but it can save you from a rejection letter for something you could have fixed in ten minutes.</p>
                    HTML,
            ],
            [
                'slug' => 'entry-test-preparation-a-30-day-plan',
                'title' => 'Entry Test Preparation: A 30-Day Plan',
                'topic' => 'entry-test', 'category' => 'jobs', 'author' => 'ali.author@example.com', 'image' => 'career-desk',
                'excerpt' => 'A realistic month-long study plan for university entry tests, with weekly goals, practice tests and revision tips.',
                'meta_title' => 'Entry Test Preparation: A 30-Day Study Plan',
                'meta_description' => 'Prepare for university entry tests in 30 days with a simple weekly plan: cover the syllabus, practise past papers, take mock tests and revise smartly.',
                'tags' => ['Entry Test Preparation', 'MDCAT', 'ECAT', 'NTS Preparation', 'Study Plan'],
                'faqs' => [
                    ['question' => 'Is 30 days enough to prepare for an entry test?', 'answer' => 'It is enough to revise and practise if you already studied the subjects in intermediate. If you are starting from zero, begin earlier.'],
                    ['question' => 'Where can I find the syllabus?', 'answer' => 'Always use the syllabus published by the body that conducts your test, since it changes from year to year.'],
                    ['question' => 'How many mock tests should I take?', 'answer' => 'Aim for at least one full timed mock test each week, then more in the final week.'],
                ],
                'related' => ['All Entry Test Guides' => '/blog?topic=entry-test', 'How to Write a CV' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan', 'Browse the latest jobs' => '/jobs'],
                'body' => <<<'HTML'
                    <p>A month is not long, but it is enough to make a real difference if you use it well. The secret is to follow a plan instead of jumping between books.</p>
                    <h2>Before You Start</h2>
                    <p>Download the official syllabus and test pattern for your test. Note how many questions each subject carries and whether there is negative marking. This tells you where to spend most of your time.</p>
                    <h2>Week 1: Cover the Basics</h2>
                    <p>Go through the high-weight topics first. Read your intermediate textbooks, since most questions are built on them, and make short notes of formulas and key facts.</p>
                    <h2>Week 2: Practise Topic by Topic</h2>
                    <p>After each topic, solve 30 to 50 questions on it. Mark the ones you get wrong and write down why. Wrong answers are where most of your learning will come from.</p>
                    <h2>Week 3: Past Papers and Speed</h2>
                    <p>Start timed practice with past papers. Many students know the answers but run out of time, so learn to move on from a hard question and come back later.</p>
                    <h2>Week 4: Mock Tests and Revision</h2>
                    <p>Take a full mock test every two or three days under exam conditions. Use the days in between to revise your notes and your list of mistakes.</p>
                    <h2>Look After Yourself</h2>
                    <ul><li>Sleep seven to eight hours. Late nights hurt memory more than they help.</li><li>Take a short break every hour.</li><li>Do not start new books in the last few days. Revise what you know.</li></ul>
                    <p>On test day, reach early, carry your admit card and CNIC or B-Form, and read every question fully before you answer.</p>
                    HTML,
            ],
            [
                'slug' => 'how-to-fill-a-government-job-application-form-correctly',
                'title' => 'How to Fill a Government Job Application Form Correctly',
                'topic' => 'job-application', 'category' => 'jobs', 'author' => 'admin.author@example.com', 'image' => 'jobs-blocks',
                'excerpt' => 'A careful, section-by-section way to fill government job forms online so a small mistake does not cost you the job.',
                'meta_title' => 'How to Fill a Government Job Application Form Correctly',
                'meta_description' => 'Step-by-step tips to fill government job application forms online in Pakistan without mistakes: personal details, education, experience, documents and fee.',
                'tags' => ['Job Application Form', 'Government Jobs', 'Online Application', 'National Jobs Portal', 'Job Application Tips'],
                'faqs' => [
                    ['question' => 'Can I edit my application after submitting?', 'answer' => 'Often you cannot, so check every field before you press submit. Some portals allow edits until the last date. Read the instructions on the portal.'],
                    ['question' => 'What photo size is needed?', 'answer' => 'Each portal sets its own size and format, often a small JPG. Read the upload instructions and resize your photo to match.'],
                    ['question' => 'What if my name is spelled differently on documents?', 'answer' => 'Use the spelling on your CNIC and try to correct the other documents early, since mismatches can cause problems at verification.'],
                ],
                'related' => ['7 Things to Check Before Applying' => '/blog/7-things-to-check-before-applying-for-a-government-job', 'Junior Auditor Jobs, Balochistan' => '/jobs/junior-auditor-jobs-bps-11-controller-general-of-accounts-balochistan', 'All Government Jobs' => '/jobs/government-jobs'],
                'body' => <<<'HTML'
                    <p>Most government jobs in Pakistan are now applied for online, through department portals or the National Jobs Portal. The forms are not difficult, but a single wrong entry can lead to rejection at the scrutiny stage. Here is how to fill one carefully.</p>
                    <h2>Before You Open the Form</h2>
                    <ul><li>Read the full advertisement, including the notes at the bottom.</li><li>Scan your documents clearly and keep them in one folder.</li><li>Resize your photo and signature to the sizes the portal asks for.</li><li>Use a laptop if you can. Mobile forms are easy to get wrong.</li></ul>
                    <h2>Personal Details</h2>
                    <p>Enter your name, father's name and date of birth exactly as they appear on your CNIC. Use a mobile number registered in your own name and an email address you check daily, because test and interview calls are often sent there.</p>
                    <h2>Domicile and Quota</h2>
                    <p>Select the province and district shown on your domicile certificate. Choose the quota only if you actually qualify for it and can prove it.</p>
                    <h2>Education and Experience</h2>
                    <p>Fill in degrees from the latest backwards, using the marks and dates on your transcripts. Do not round up marks. For experience, enter exact start and end dates and keep the experience letter ready.</p>
                    <h2>Documents and Fee</h2>
                    <p>Upload each document in the right box. Pay the fee only by the method the advert gives, and enter the challan or transaction number carefully.</p>
                    <h2>Review, Submit and Save</h2>
                    <p>Use the preview page to read every field once more. After submitting, download or print the application slip and keep it with your fee receipt. You will need both later.</p>
                    HTML,
            ],
            [
                'slug' => 'how-to-write-a-cover-letter-for-jobs-in-pakistan',
                'title' => 'How to Write a Cover Letter for Jobs in Pakistan',
                'topic' => 'cv-resume', 'category' => 'jobs', 'author' => 'sara.author@example.com', 'image' => 'career-office',
                'excerpt' => 'A short, polite cover letter can set you apart. Here is a simple structure you can adapt for any job.',
                'meta_title' => 'How to Write a Cover Letter for Jobs in Pakistan',
                'meta_description' => 'Learn how to write a short, professional cover letter for jobs in Pakistan, with a simple four-paragraph structure and tips for private and government applications.',
                'tags' => ['Cover Letter', 'Job Application', 'CV Writing', 'Private Jobs', 'Fresh Graduates'],
                'faqs' => [
                    ['question' => 'Do I need a cover letter for every job?', 'answer' => 'Send one whenever the employer accepts it, especially for private jobs and email applications. Online government forms usually do not have space for one.'],
                    ['question' => 'How long should a cover letter be?', 'answer' => 'Keep it to three or four short paragraphs, well under one page.'],
                    ['question' => 'Can I use the same letter for every job?', 'answer' => 'Keep a template, but change the opening and the middle paragraph so they speak to each job.'],
                ],
                'related' => ['How to Write a CV' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan', 'Private Jobs' => '/jobs/private-jobs', 'Bank Jobs' => '/jobs/bank-jobs'],
                'body' => <<<'HTML'
                    <p>A CV lists what you have done. A cover letter explains why you want this particular job and why you would be good at it. For private sector roles and email applications, a short letter often makes the difference between two similar CVs.</p>
                    <h2>A Simple Four-Part Structure</h2>
                    <ol><li><strong>Opening:</strong> name the job you are applying for and where you saw it.</li><li><strong>Why you fit:</strong> two or three points from your experience or studies that match the job.</li><li><strong>Why this employer:</strong> one or two lines showing you know what the organisation does.</li><li><strong>Close:</strong> thank them and say you would welcome an interview.</li></ol>
                    <h2>Keep the Tone Professional and Warm</h2>
                    <p>Address the letter to a person if you know their name. Otherwise "Dear Hiring Manager" is fine. Write in clear, simple English and avoid copied phrases that every applicant uses.</p>
                    <h2>Show, Do Not Just Tell</h2>
                    <p>"I am hardworking" means little on its own. "During my internship I handled customer queries for a branch of over 40 accounts" shows it. Pick one real example that matches the job.</p>
                    <h2>Sending by Email</h2>
                    <p>If you apply by email, put a short version of the letter in the email body and attach your CV as a PDF. Use a clear subject line such as <em>Application for Accounts Officer, Your Name</em>.</p>
                    <h2>Final Check</h2>
                    <p>Make sure the company name and job title are correct. Sending a letter with another company's name in it is one of the most common, and most avoidable, mistakes.</p>
                    HTML,
            ],
            [
                'slug' => 'common-job-interview-questions-and-how-to-answer-them',
                'title' => 'Common Job Interview Questions and How to Answer Them',
                'topic' => 'interview', 'category' => 'jobs', 'author' => 'ali.author@example.com', 'image' => 'career-office',
                'excerpt' => 'The questions you are most likely to hear in an interview, with a simple way to answer each one honestly.',
                'meta_title' => 'Common Job Interview Questions and How to Answer Them',
                'meta_description' => 'Prepare for the most common job interview questions in Pakistan, from "tell us about yourself" to salary expectations, with simple ways to answer each one.',
                'tags' => ['Interview Questions', 'Interview Preparation', 'Job Interview Tips', 'HR Interview', 'Panel Interview'],
                'faqs' => [
                    ['question' => 'How should I answer "tell us about yourself"?', 'answer' => 'Give a one-minute summary of your education, your most relevant experience and why you want this job. Keep personal details short.'],
                    ['question' => 'How do I answer a question about my weakness?', 'answer' => 'Name a real but manageable weakness and explain what you are doing to improve it.'],
                    ['question' => 'Should I ask questions at the end?', 'answer' => 'Yes. One or two thoughtful questions about the role or team show genuine interest.'],
                ],
                'related' => ['How to Prepare for a Job Interview' => '/blog/how-to-prepare-for-a-job-interview-in-pakistan', 'How to Write a CV' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan', 'Browse the latest jobs' => '/jobs'],
                'body' => <<<'HTML'
                    <p>Interviews feel unpredictable, but the same handful of questions come up again and again. Prepare honest answers to these and you will walk in far calmer.</p>
                    <h2>"Tell Us About Yourself"</h2>
                    <p>This is your one-minute introduction. Mention your education, your most relevant experience and why this job interests you. Leave out your full life story.</p>
                    <h2>"Why Do You Want This Job?"</h2>
                    <p>Connect your skills to what the organisation does. A good answer shows you read the advert and understand the role, not just that you need a job.</p>
                    <h2>"What Are Your Strengths?"</h2>
                    <p>Pick two strengths that matter for this role and give a short example of each. Examples are far more convincing than a list of adjectives.</p>
                    <h2>"What Is Your Weakness?"</h2>
                    <p>Be honest but sensible. Name something real, such as public speaking, and explain what you are doing about it. Avoid answers like "I work too hard".</p>
                    <h2>"Where Do You See Yourself in Five Years?"</h2>
                    <p>Show that you want to grow in this field. Talk about skills you want to build and responsibilities you hope to take on.</p>
                    <h2>"Why Should We Hire You?"</h2>
                    <p>Sum up in two or three sentences how your skills and attitude match what they need.</p>
                    <h2>"What Are Your Salary Expectations?"</h2>
                    <p>For private jobs, research the usual range for the role in your city and give a reasonable range. For government posts the pay scale is fixed, so this question is rare.</p>
                    <h2>Your Turn to Ask</h2>
                    <p>End with one or two questions, such as what a typical day looks like or how performance is measured. It shows real interest.</p>
                    HTML,
            ],
            [
                'slug' => 'in-demand-skills-for-jobs-in-pakistan',
                'title' => 'In-Demand Skills That Help You Get a Job in Pakistan',
                'topic' => 'career-development', 'category' => 'jobs', 'author' => 'admin.author@example.com', 'image' => 'jobs-illustration',
                'excerpt' => 'Practical skills employers value across government and private jobs, and simple ways to start building them.',
                'meta_title' => 'In-Demand Skills for Jobs in Pakistan and How to Learn Them',
                'meta_description' => 'Practical skills that help job seekers in Pakistan stand out, from communication and MS Excel to digital skills, and simple ways to start learning them.',
                'tags' => ['Career Development', 'Job Skills', 'Digital Skills', 'MS Excel', 'Career Planning'],
                'faqs' => [
                    ['question' => 'Which skill should I learn first?', 'answer' => 'Start with the skill that appears most often in adverts for the jobs you want. For many office roles that is MS Office, especially Excel.'],
                    ['question' => 'Do I need a paid course?', 'answer' => 'Not always. Many skills can be learned through free online material and practice. A certificate helps most when the employer asks for one.'],
                    ['question' => 'How do I show skills without experience?', 'answer' => 'Use small projects, internships and volunteer work, and describe what you did with them in your CV.'],
                ],
                'related' => ['How to Write a CV' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan', 'Online & Work From Home Jobs' => '/jobs/online-work-from-home-jobs', 'Browse the latest jobs' => '/jobs'],
                'body' => <<<'HTML'
                    <p>Degrees open doors, but skills often decide who gets the job. The good news is that many of the skills employers want can be learned at home, at low cost, in a few months.</p>
                    <h2>Communication</h2>
                    <p>Clear writing and confident speaking matter in almost every job. Practise writing short, polite emails and explaining an idea in two or three sentences. Reading English newspapers regularly helps more than people expect.</p>
                    <h2>MS Office, Especially Excel</h2>
                    <p>Office jobs in both government and private sectors expect basic computer skills. Learning Excel formulas, tables and simple charts makes you useful from the first day.</p>
                    <h2>Digital and Online Skills</h2>
                    <p>Skills such as graphic design, content writing, basic web work and social media management are useful for local jobs and for freelancing. Pick one area and build a small portfolio of real work.</p>
                    <h2>Problem Solving and Reliability</h2>
                    <p>Employers remember people who finish tasks on time and find solutions instead of excuses. Show this in your CV with examples from studies, internships or part-time work.</p>
                    <h2>Language and Customer Service</h2>
                    <p>Being comfortable in English, Urdu and your regional language helps in banks, call centres and public-facing roles.</p>
                    <h2>How to Start</h2>
                    <ol><li>Look at ten adverts for the job you want and note the skills that repeat.</li><li>Choose one skill and practise it for 30 minutes a day.</li><li>Add a line to your CV as soon as you can show real work.</li></ol>
                    HTML,
            ],
            [
                'slug' => 'how-to-find-jobs-abroad-safely-from-pakistan',
                'title' => 'How to Find Jobs Abroad Safely from Pakistan',
                'topic' => 'working-abroad', 'category' => 'jobs', 'author' => 'ali.author@example.com', 'image' => 'career-globe',
                'excerpt' => 'How to look for overseas jobs through legal channels, spot fake agents and protect your money and documents.',
                'meta_title' => 'How to Find Jobs Abroad Safely from Pakistan',
                'meta_description' => 'Find overseas jobs safely from Pakistan: use licensed overseas employment promoters, check offers with the Bureau of Emigration, and avoid fake visa agents.',
                'tags' => ['Jobs Abroad', 'Overseas Employment', 'Bureau of Emigration', 'Protector of Emigrants', 'Work Visa'],
                'faqs' => [
                    ['question' => 'How do I know an overseas employment agent is genuine?', 'answer' => 'Licensed overseas employment promoters are registered with the Bureau of Emigration & Overseas Employment. Check the licence on the Bureau\'s official website before paying anything.'],
                    ['question' => 'What is protector registration?', 'answer' => 'Workers going abroad for employment are registered at the Protector of Emigrants office before departure. The Bureau of Emigration explains the process.'],
                    ['question' => 'Should I hand over my passport to an agent?', 'answer' => 'Be very careful. Only give documents to a licensed promoter, against a written receipt, and keep copies of everything.'],
                ],
                'related' => ['How to Write a CV' => '/blog/how-to-write-a-cv-tips-for-jobs-in-pakistan', 'In-Demand Skills for Jobs' => '/blog/in-demand-skills-for-jobs-in-pakistan', 'Browse jobs in Pakistan' => '/jobs'],
                'body' => <<<'HTML'
                    <p>Working abroad can change a family's future, but it is also where many job seekers lose their savings to fake agents. Going through legal channels takes a little longer, and it is far safer.</p>
                    <h2>Use Licensed Overseas Employment Promoters</h2>
                    <p>In Pakistan, recruitment for jobs abroad is regulated by the <a href="https://beoe.gov.pk" target="_blank" rel="noopener">Bureau of Emigration &amp; Overseas Employment</a>. Licensed overseas employment promoters are registered with the Bureau. Before you pay any fee, check the promoter's licence on the Bureau's official website.</p>
                    <h2>Know the Warning Signs</h2>
                    <ul><li>A job offer with no interview or skills test.</li><li>Pressure to pay large amounts in cash quickly.</li><li>No written contract, or a contract you are not allowed to read.</li><li>Promises of a "free visa" or very high pay for unskilled work.</li><li>An agent who cannot show a valid licence.</li></ul>
                    <h2>Read Your Contract Carefully</h2>
                    <p>Your employment contract should state the job title, salary, working hours, accommodation and the length of the contract. Ask someone you trust to read it with you before you sign.</p>
                    <h2>Complete the Official Process</h2>
                    <p>Workers going abroad for employment are registered with the Protector of Emigrants before departure. Follow the steps the Bureau of Emigration sets out, and keep copies of your contract, visa and receipts.</p>
                    <h2>Prepare Your Skills and Documents</h2>
                    <p>Employers abroad value trade certificates, driving licences and some English. Keep your passport valid, your educational and experience documents attested where required, and your CV up to date.</p>
                    <p>If a deal sounds too good to be true, take a day, check the agent and ask questions. A genuine employer will still be there tomorrow.</p>
                    HTML,
            ],
        ];
    }
}
