<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $site = config('app.name');

        $pages = [
            'about-us' => ['About Us', "<p>{$site} is an information website for Pakistan that brings together jobs, government schemes, BISP 8171, admissions, news and home decor in one place.</p><p>Our aim is to explain complicated information in simple language and to reference the official source in every guide.</p>"],
            'contact-us' => ['Contact Us', '<p>Have a question, correction or suggestion? Contact us using the form below.</p>'],
            'privacy-policy' => ['Privacy Policy', "<p>{$site} collects only the information you choose to give us, such as your email address for the newsletter or your contact form details.</p><h2>How we use your information</h2><p>Your information is used only to send updates and reply to your questions. It is never sold.</p><h2>Cookies</h2><p>This website uses the cookies needed for sessions and security.</p>"],
            'disclaimer' => ['Disclaimer', "<p>{$site} is an information website, not an official government portal. We are not affiliated with any government organization.</p><p>Always confirm details with the official source before applying. We do not charge a fee for any application.</p>"],
            'terms-and-conditions' => ['Terms & Conditions', '<p>By using this website, you agree to these terms. The information here is for general guidance only.</p><p>Copying content without permission is not allowed.</p>'],
            'editorial-policy' => ['Editorial Policy', '<p>Every post is checked against an official or reliable source, and the source link is included.</p><p>Older information is labelled "Outdated", and content is corrected when we are told about a mistake.</p>'],
            'write-for-us' => ['Write for Us', '<p>Would you like to write about jobs, education, careers or home decor? Send us your topic and a writing sample through the <a href="/contact-us">Contact Us</a> page.</p><p>Articles must be original and include their sources.</p>'],
        ];

        foreach ($pages as $slug => [$title, $body]) {
            Page::updateOrCreate(['slug' => $slug], ['title' => $title, 'body' => $body, 'status' => 'published']);
        }
    }
}
