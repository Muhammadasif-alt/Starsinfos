<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Moves seeded pages and sample article bodies from Roman Urdu to English on existing installs.
 * A row changes only if it still contains its original seeded text, so admin edits are kept.
 */
return new class extends Migration
{
    /** slug => text found only in the original Roman Urdu body */
    private const PAGE_MARKERS = [
        'about-us' => 'maloomat aik jagah milti hai',
        'contact-us' => 'Neeche diye gaye form',
        'privacy-policy' => 'Maloomat ka istemal',
        'disclaimer' => 'Hamara kisi government idaray',
        'terms-and-conditions' => 'Is website ka istemal karne se',
        'editorial-policy' => 'Har post official ya qabil-e-aitmad',
        'write-for-us' => 'likhna chahte hain',
    ];

    public function up(): void
    {
        $this->translatePages();
        $this->translatePosts();
        $this->translateBlogPosts();
    }

    private function translatePages(): void
    {
        $site = config('app.name');

        $bodies = [
            'about-us' => "<p>{$site} is an information website for Pakistan that brings together jobs, government schemes, BISP 8171, admissions, news and home decor in one place.</p><p>Our aim is to explain complicated information in simple language and to reference the official source in every guide.</p>",
            'contact-us' => '<p>Have a question, correction or suggestion? Contact us using the form below.</p>',
            'privacy-policy' => "<p>{$site} collects only the information you choose to give us, such as your email address for the newsletter or your contact form details.</p><h2>How we use your information</h2><p>Your information is used only to send updates and reply to your questions. It is never sold.</p><h2>Cookies</h2><p>This website uses the cookies needed for sessions and security.</p>",
            'disclaimer' => "<p>{$site} is an information website, not an official government portal. We are not affiliated with any government organization.</p><p>Always confirm details with the official source before applying. We do not charge a fee for any application.</p>",
            'terms-and-conditions' => '<p>By using this website, you agree to these terms. The information here is for general guidance only.</p><p>Copying content without permission is not allowed.</p>',
            'editorial-policy' => '<p>Every post is checked against an official or reliable source, and the source link is included.</p><p>Older information is labelled "Outdated", and content is corrected when we are told about a mistake.</p>',
            'write-for-us' => '<p>Would you like to write about jobs, education, careers or home decor? Send us your topic and a writing sample through the <a href="/contact-us">Contact Us</a> page.</p><p>Articles must be original and include their sources.</p>',
        ];

        foreach (self::PAGE_MARKERS as $slug => $marker) {
            DB::table('pages')->where('slug', $slug)->where('body', 'like', "%{$marker}%")->update(['body' => $bodies[$slug]]);
        }
    }

    private function translatePosts(): void
    {
        DB::table('posts')->where('body', 'like', '%yeh demo text hai%')->orderBy('id')->each(function (object $post) {
            DB::table('posts')->where('id', $post->id)->update(['body' => <<<HTML
                <p><strong>Sample content:</strong> this is demo text. Replace it with information verified from the official source before publishing.</p>
                <p>This guide gives general information about {$post->title}.</p>
                <h2>Eligibility</h2>
                <ul><li>Read the requirements in the official notification.</li><li>Keep the required documents ready in advance.</li></ul>
                <h2>How to Apply</h2>
                <ol><li>Open the official website.</li><li>Fill in the form carefully.</li><li>Submit it before the last date.</li></ol>
                HTML]);
        });
    }

    private function translateBlogPosts(): void
    {
        $paragraph = '<p>Sample content: add the final text of this article from the admin panel. '.str_repeat('Read the official instructions at every step, keep your documents ready and finish each task on time. ', 12).'</p>';

        DB::table('blog_posts')->where('body', 'like', '%is article ka asal text%')->orderBy('id')->each(function (object $post) use ($paragraph) {
            $body = "<p>{$post->excerpt}</p><h2>Getting Started</h2>{$paragraph}<h2>Key Points</h2>{$paragraph}<h2>Final Advice</h2>{$paragraph}";

            DB::table('blog_posts')->where('id', $post->id)->update([
                'body' => $body,
                'read_time' => max(1, (int) ceil(str_word_count(strip_tags($body)) / 200)),
            ]);
        });
    }
};
