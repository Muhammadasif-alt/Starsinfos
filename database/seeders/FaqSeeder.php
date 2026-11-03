<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    private const FAQS = [
        ['Is this an official government website?', 'No. This is an information website only. Always confirm details on the official portal.'],
        ['Can I submit my own article?', 'Yes. Send us your topic and a writing sample through our Contact Us page.'],
        ['Do I have to pay to apply for a job or scheme?', 'No. Stars Infos is free to use, and we never ask for money. Official forms only charge the fee written in the official advertisement. Never pay an agent who promises selection.'],
        ['How do I apply for a job or scheme listed here?', 'Open the listing, read the eligibility and required documents, then use the official link on the page to apply on the official website before the last date.'],
        ['How often is the information updated?', 'We add new jobs, schemes and articles as soon as official notices are published, and we mark closed listings. Always check the last date on the official website.'],
    ];

    public function run(): void
    {
        foreach (self::FAQS as $order => [$question, $answer]) {
            Faq::updateOrCreate(['question' => $question], ['answer' => $answer, 'sort_order' => $order]);
        }
    }
}
