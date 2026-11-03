<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Three more homepage FAQs, added after the existing ones. */
return new class extends Migration
{
    private const FAQS = [
        ['Do I have to pay to apply for a job or scheme?', 'No. Stars Infos is free to use, and we never ask for money. Official forms only charge the fee written in the official advertisement. Never pay an agent who promises selection.'],
        ['How do I apply for a job or scheme listed here?', 'Open the listing, read the eligibility and required documents, then use the official link on the page to apply on the official website before the last date.'],
        ['How often is the information updated?', 'We add new jobs, schemes and articles as soon as official notices are published, and we mark closed listings. Always check the last date on the official website.'],
    ];

    public function up(): void
    {
        $order = (int) DB::table('faqs')->max('sort_order');

        foreach (self::FAQS as [$question, $answer]) {
            if (DB::table('faqs')->where('question', $question)->exists()) {
                continue;
            }

            DB::table('faqs')->insert([
                'question' => $question,
                'answer' => $answer,
                'sort_order' => ++$order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
