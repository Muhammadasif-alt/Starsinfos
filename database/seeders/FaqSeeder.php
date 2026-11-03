<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    private const FAQS = [
        ['Is this an official government website?', 'No. This is an information website only. Always confirm details on the official portal.'],
        ['Can I submit my own article?', 'Yes. Send us your topic and a writing sample through our Contact Us page.'],
    ];

    public function run(): void
    {
        foreach (self::FAQS as $order => [$question, $answer]) {
            Faq::updateOrCreate(['question' => $question], ['answer' => $answer, 'sort_order' => $order]);
        }
    }
}
