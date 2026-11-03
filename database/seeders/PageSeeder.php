<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    private const EXCERPTS = [
        'about-us' => 'Clear, practical information on jobs, government schemes, careers and style for people across Pakistan.',
        'contact-us' => 'Have a question, a correction or a suggestion? We would like to hear from you.',
        'privacy-policy' => 'What information we collect, how we use it and how we protect it.',
        'disclaimer' => 'Important information about the purpose and limits of this website.',
        'terms-and-conditions' => 'The terms that apply when you use this website.',
        'editorial-policy' => 'How we research, check and update the information we publish.',
    ];

    public function run(): void
    {
        $site = config('app.name');

        $pages = [
            'about-us' => ['About Us', "<p>{$site} is an information website for Pakistan that brings together jobs, government schemes, beauty and style tips in one place.</p><p>Our aim is to explain complicated information in simple language and to reference the official source in every guide.</p>"],
            'contact-us' => ['Contact Us', '<p>Have a question, correction or suggestion? Contact us using the form below.</p>'],
            'privacy-policy' => ['Privacy Policy', "<p>{$site} collects only the information you choose to give us, such as your email address for the newsletter or your contact form details.</p><h2>How we use your information</h2><p>Your information is used only to send updates and reply to your questions. It is never sold.</p><h2>Cookies</h2><p>This website uses the cookies needed for sessions and security.</p>"],
            'disclaimer' => ['Disclaimer', "<p>{$site} is an information website, not an official government portal. We are not affiliated with any government organization.</p><p>Always confirm details with the official source before applying. We do not charge a fee for any application.</p>"],
            'terms-and-conditions' => ['Terms & Conditions', '<p>By using this website, you agree to these terms. The information here is for general guidance only.</p><p>Copying content without permission is not allowed.</p>'],
            'editorial-policy' => ['Editorial Policy', '<p>Every post is checked against an official or reliable source, and the source link is included.</p><p>Older information is labelled "Outdated", and content is corrected when we are told about a mistake.</p>'],
        ];

        foreach ($pages as $slug => [$title, $body]) {
            Page::updateOrCreate(['slug' => $slug], ['title' => $title, 'excerpt' => self::EXCERPTS[$slug], 'body' => $body, 'status' => 'published']);
        }
    }
}
