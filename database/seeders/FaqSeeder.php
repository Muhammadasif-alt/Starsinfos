<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    private const FAQS = [
        ['Kya yeh official government website hai?', 'Nahi. Yeh sirf information website hai. Hamesha official portal se tasdeeq karein.'],
        ['BISP 8171 ki information kitni nayi hoti hai?', 'Hum updates ko regularly check karte hain, aur purani information par "Outdated" label lagate hain.'],
        ['Kya main apna article bhej sakta hun?', 'Ji haan, "Write for Us" page par rabta karein.'],
    ];

    public function run(): void
    {
        foreach (self::FAQS as $order => [$question, $answer]) {
            Faq::updateOrCreate(['question' => $question], ['answer' => $answer, 'sort_order' => $order]);
        }
    }
}
