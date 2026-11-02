<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Adds a fourth sample article so the homepage Career Advice layout has one lead and three side posts. */
return new class extends Migration
{
    private const SLUG = 'how-to-prepare-for-a-job-interview-in-pakistan';

    public function up(): void
    {
        $author = DB::table('users')->where('email', 'sara.author@example.com')->value('id');

        if (! $author || DB::table('blog_posts')->where('slug', self::SLUG)->exists()) {
            return;
        }

        $excerpt = 'Simple steps to research the employer, practise your answers and make a good impression.';
        $paragraph = '<p>Sample content: add the final text of this article from the admin panel. '.str_repeat('Read the official instructions at every step, keep your documents ready and finish each task on time. ', 12).'</p>';
        $body = "<p>{$excerpt}</p><h2>Getting Started</h2>{$paragraph}<h2>Key Points</h2>{$paragraph}<h2>Final Advice</h2>{$paragraph}";

        DB::table('blog_posts')->insert([
            'author_id' => $author,
            'category_id' => DB::table('categories')->where('slug', 'jobs')->value('id'),
            'title' => 'How to Prepare for a Job Interview in Pakistan',
            'slug' => self::SLUG,
            'excerpt' => $excerpt,
            'body' => $body,
            'featured_image' => 'seed/jobs-healthcare.jpg',
            'read_time' => max(1, (int) ceil(str_word_count(strip_tags($body)) / 200)),
            'status' => 'published',
            'is_featured' => false,
            'published_at' => now()->subDays(3),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
