<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Gives the footer an editable description and turns the seeded government scheme
 * article into a career article. The article changes only if its seeded title is intact.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            'key' => 'footer_text',
            'value' => json_encode('Find government, private, bank and teaching jobs in Pakistan, with last dates and links to official sources. We also share career advice, government schemes, admissions and news to help you take your next step. This is an information website, not an official government portal, so always confirm details with the official source.'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('blog_posts')
            ->where('title', '7 Things to Know Before Applying for a Government Scheme')
            ->update([
                'title' => '7 Things to Check Before Applying for a Government Job',
                'slug' => '7-things-to-check-before-applying-for-a-government-job',
                'excerpt' => 'Documents, eligibility and the common mistakes that get job applications rejected.',
                'category_id' => DB::table('categories')->where('slug', 'jobs')->value('id'),
                'featured_image' => 'seed/jobs-banner.jpg',
            ]);
    }
};
