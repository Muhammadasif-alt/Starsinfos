<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            CategorySeeder::class,
            PageSeeder::class,
            PostSeeder::class,
            BlogPostSeeder::class,
            FaqSeeder::class,
            BeautyContentSeeder::class,
            ArticleSeeder::class,
        ]);

        // Older seeders still define these short duplicates; the fuller guides replace them.
        \App\Models\Post::whereIn('slug', \App\Support\RetiredPosts::slugs())->delete();
    }
}
