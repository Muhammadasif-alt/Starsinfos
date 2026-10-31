<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    private const FAQS = [
        ['Is this an official government website?', 'No. This is an information website only. Always confirm details on the official portal.'],
        ['How up to date is the BISP 8171 information?', 'We check for updates regularly and label older information as "Outdated".'],
        ['Can I submit my own article?', 'Yes. Please get in touch through our "Write for Us" page.'],
    ];

    public function run(): void
    {
        foreach (self::FAQS as $order => [$question, $answer]) {
            Faq::updateOrCreate(['question' => $question], ['answer' => $answer, 'sort_order' => $order]);
        }
    }
}
