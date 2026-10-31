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
            'about-us' => ['About Us', "<p>{$site} Pakistan ke liye ek information website hai jahan jobs, government schemes, BISP 8171, admissions, news aur home decor ki maloomat aik jagah milti hai.</p><p>Hamara maqsad mushkil maloomat ko aasan zuban mein pesh karna aur har guide ke saath official source ka hawala dena hai.</p>"],
            'contact-us' => ['Contact Us', '<p>Koi sawal, correction ya tajweez hai? Neeche diye gaye form ke zariye hum se rabta karein.</p>'],
            'privacy-policy' => ['Privacy Policy', "<p>{$site} sirf wahi maloomat jama karta hai jo aap khud dete hain, jaise newsletter ke liye email ya contact form ki details.</p><h2>Maloomat ka istemal</h2><p>Aap ki maloomat sirf updates bhejne aur aap ke sawal ka jawab dene ke liye istemal hoti hai, aur farokht nahi ki jati.</p><h2>Cookies</h2><p>Website session aur security ke liye zaroori cookies istemal karti hai.</p>"],
            'disclaimer' => ['Disclaimer', "<p>{$site} ek information website hai, official government portal nahi. Hamara kisi government idaray se koi talluq nahi.</p><p>Apply karne se pehle hamesha official source se tasdeeq kar lein. Hum kisi application ke liye fee nahi lete.</p>"],
            'terms-and-conditions' => ['Terms & Conditions', '<p>Is website ka istemal karne se aap in sharait se ittefaq karte hain. Yahan di gayi maloomat sirf aam rehnumai ke liye hai.</p><p>Content ko baghair ijazat copy karna mana hai.</p>'],
            'editorial-policy' => ['Editorial Policy', '<p>Har post official ya qabil-e-aitmad source se check ki jati hai aur source link saath diya jata hai.</p><p>Purani maloomat par "Outdated" label lagaya jata hai, aur ghalti ki itla milne par content theek kiya jata hai.</p>'],
            'write-for-us' => ['Write for Us', '<p>Jobs, education, career ya home decor par likhna chahte hain? Apna topic aur sample <a href="/contact-us">Contact Us</a> page se bhejein.</p><p>Article original aur source ke saath hona chahiye.</p>'],
        ];

        foreach ($pages as $slug => [$title, $body]) {
            Page::updateOrCreate(['slug' => $slug], ['title' => $title, 'body' => $body, 'status' => 'published']);
        }
    }
}
